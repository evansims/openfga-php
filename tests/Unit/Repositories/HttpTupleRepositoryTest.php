<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Repositories;

use DateTimeImmutable;
use OpenFGA\Exceptions\{ClientException, NetworkError, SerializationError};
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use OpenFGA\Models\Collections\{TupleKeys, TupleKeysInterface};
use OpenFGA\Models\{TupleKey};
use OpenFGA\Repositories\HttpTupleRepository;
use OpenFGA\Requests\{ListTupleChangesRequest, ReadTuplesRequest, WriteTuplesRequest};
use OpenFGA\Responses\{ListTupleChangesResponse, ReadTuplesResponse, WriteTuplesResponse};
use OpenFGA\Results\{Failure, Success};
use OpenFGA\Schemas\SchemaValidator;
use OpenFGA\Services\{HttpServiceInterface, TupleFilterServiceInterface};
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface, StreamInterface};
use RuntimeException;

describe('HttpTupleRepository', function (): void {
    beforeEach(function (): void {
        /** @var HttpServiceInterface&MockObject */
        $this->httpService = test()->createMock(HttpServiceInterface::class);

        /** @var MockObject&TupleFilterServiceInterface */
        $this->tupleFilterService = test()->createMock(TupleFilterServiceInterface::class);

        /** @var SchemaValidator */
        $this->validator = new SchemaValidator;

        /** @var MockObject&StoreInterface */
        $this->store = test()->createMock(StoreInterface::class);
        $this->store->method('getId')->willReturn('store-123');

        /** @var AuthorizationModelInterface&MockObject */
        $this->model = test()->createMock(AuthorizationModelInterface::class);
        $this->model->method('getId')->willReturn('model-456');

        $this->repository = new HttpTupleRepository(
            $this->httpService,
            $this->tupleFilterService,
            $this->validator,
        );

        // Helper to create a properly formatted HTTP response
        $this->createHttpResponse = function (int $statusCode, string $body): HttpResponseInterface {
            $stream = test()->createMock(StreamInterface::class);
            $stream->method('getContents')->willReturn($body);
            $stream->method('__toString')->willReturn($body);

            $response = test()->createMock(HttpResponseInterface::class);
            $response->method('getStatusCode')->willReturn($statusCode);
            $response->method('getBody')->willReturn($stream);

            return $response;
        };
    });

    describe('write()', function (): void {
        test('successfully writes tuples in transactional mode', function (): void {
            $tuples = new TupleKeys([
                new TupleKey('user:alice', 'reader', 'document:budget'),
                new TupleKey('user:bob', 'editor', 'document:roadmap'),
            ]);

            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->with($tuples, null)
                ->willReturn([$tuples, null]);

            $httpResponse = test()->createMock(HttpResponseInterface::class);
            $httpResponse->method('getStatusCode')->willReturn(200);

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (WriteTuplesRequest $request): bool {
                    expect($request->getStore())->toBe('store-123');
                    expect($request->getModel())->toBe('model-456');
                    expect($request->isTransactional())->toBeTrue();
                    expect($request->getWrites())->toBeInstanceOf(TupleKeysInterface::class);
                    expect($request->getWrites()->count())->toBe(2);
                    expect($request->getDeletes())->toBeNull();

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->write($this->store, $this->model, $tuples);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(WriteTuplesResponse::class);
            expect($result->unwrap()->isTransactional())->toBeTrue();
            expect($result->unwrap()->isCompleteSuccess())->toBeTrue();
        });

        test('successfully writes tuples in non-transactional mode', function (): void {
            $tuples = new TupleKeys([
                new TupleKey('user:alice', 'reader', 'document:budget'),
                new TupleKey('user:bob', 'editor', 'document:roadmap'),
            ]);

            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->with($tuples, null)
                ->willReturn([$tuples, null]);

            $httpResponse = test()->createMock(HttpResponseInterface::class);
            $httpResponse->method('getStatusCode')->willReturn(200);

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            // Expect 2 sends for 2 chunks (with 1 tuple per chunk)
            $this->httpService
                ->expects(test()->exactly(2))
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->exactly(2))
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->write(
                $this->store,
                $this->model,
                $tuples,
                transactional: false,
                options: [
                    'maxParallelRequests' => 2,
                    'maxTuplesPerChunk' => 1,
                    'maxRetries' => 1,
                    'retryDelaySeconds' => 0.1,
                ],
            );

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(WriteTuplesResponse::class);
            expect($result->unwrap()->isTransactional())->toBeFalse();
            expect($result->unwrap()->getTotalOperations())->toBe(2);
            expect($result->unwrap()->getTotalChunks())->toBe(2);
        });

        test('returns success with empty response when no tuples to write', function (): void {
            $emptyTuples = new TupleKeys([]);

            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->with($emptyTuples, null)
                ->willReturn([null, null]);

            $this->httpService->expects(test()->never())->method('send');

            $result = $this->repository->write($this->store, $this->model, $emptyTuples);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(WriteTuplesResponse::class);
            expect($result->unwrap()->getTotalOperations())->toBe(0);
            expect($result->unwrap()->getTotalChunks())->toBe(0);
        });

        test('fails when transactional limit is exceeded', function (): void {
            $tuples = new TupleKeys([]);

            for ($i = 0; 101 > $i; $i++) {
                $tuples->add(new TupleKey("user:user{$i}", 'reader', "document:doc{$i}"));
            }

            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->with($tuples, null)
                ->willReturn([$tuples, null]);

            $this->httpService->expects(test()->never())->method('send');

            $result = $this->repository->write($this->store, $this->model, $tuples);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBeInstanceOf(ClientException::class);
            expect($result->err()->getMessage())->toContain('101');
        });

        test('handles HTTP service exceptions', function (): void {
            $tuples = new TupleKeys([
                new TupleKey('user:alice', 'reader', 'document:budget'),
            ]);

            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->willReturn([$tuples, null]);

            $exception = NetworkError::Request->exception(context: ['message' => 'Connection failed']);
            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willThrowException($exception);

            $result = $this->repository->write($this->store, $this->model, $tuples);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(WriteTuplesResponse::class);
            expect($result->unwrap()->isCompleteFailure())->toBeTrue();
            expect($result->unwrap()->getFirstError())->toBe($exception);
        });

        test('handles non-transactional writes with retries', function (): void {
            $tuples = new TupleKeys([
                new TupleKey('user:alice', 'reader', 'document:budget'),
            ]);

            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->willReturn([$tuples, null]);

            $httpResponse = test()->createMock(HttpResponseInterface::class);
            $httpResponse->method('getStatusCode')->willReturn(200);

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            // Simulate first attempt failing, second succeeding
            $this->httpService
                ->expects(test()->exactly(2))
                ->method('send')
                ->willReturnOnConsecutiveCalls(
                    test()->throwException(NetworkError::Request->exception(context: ['message' => 'Temporary failure'])),
                    $httpResponse,
                );

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->write(
                $this->store,
                $this->model,
                $tuples,
                transactional: false,
                options: [
                    'maxRetries' => 1,
                    'retryDelaySeconds' => 0.001,
                ],
            );

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap()->isCompleteSuccess())->toBeTrue();
        });

        test('stops on first error when configured', function (): void {
            $tuples = new TupleKeys([
                new TupleKey('user:alice', 'reader', 'document:budget'),
                new TupleKey('user:bob', 'editor', 'document:roadmap'),
            ]);

            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->willReturn([$tuples, null]);

            $exception = NetworkError::Request->exception(context: ['message' => 'First chunk failed']);
            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willThrowException($exception);

            $result = $this->repository->write(
                $this->store,
                $this->model,
                $tuples,
                transactional: false,
                options: [
                    'maxTuplesPerChunk' => 1,
                    'stopOnFirstError' => true,
                ],
            );

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap()->getTotalChunks())->toBe(2);
            expect($result->unwrap()->getFailedChunks())->toBe(1);
            expect($result->unwrap()->getSuccessfulChunks())->toBe(0);
        });
    });

    describe('delete()', function (): void {
        test('successfully deletes tuples in transactional mode', function (): void {
            $tuples = new TupleKeys([
                new TupleKey('user:alice', 'reader', 'document:old'),
                new TupleKey('user:bob', 'editor', 'document:archived'),
            ]);

            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->with(null, $tuples)
                ->willReturn([null, $tuples]);

            $httpResponse = test()->createMock(HttpResponseInterface::class);
            $httpResponse->method('getStatusCode')->willReturn(200);

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (WriteTuplesRequest $request): bool {
                    expect($request->getStore())->toBe('store-123');
                    expect($request->getModel())->toBe('model-456');
                    expect($request->isTransactional())->toBeTrue();
                    expect($request->getWrites())->toBeNull();
                    expect($request->getDeletes())->toBeInstanceOf(TupleKeysInterface::class);
                    expect($request->getDeletes()->count())->toBe(2);

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->delete($this->store, $this->model, $tuples);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(WriteTuplesResponse::class);
            expect($result->unwrap()->isTransactional())->toBeTrue();
            expect($result->unwrap()->isCompleteSuccess())->toBeTrue();
        });

        test('successfully deletes tuples in non-transactional mode', function (): void {
            $tuples = new TupleKeys([
                new TupleKey('user:alice', 'reader', 'document:old'),
            ]);

            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->willReturn([null, $tuples]);

            $httpResponse = test()->createMock(HttpResponseInterface::class);
            $httpResponse->method('getStatusCode')->willReturn(200);

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->delete(
                $this->store,
                $this->model,
                $tuples,
                transactional: false,
            );

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap()->isTransactional())->toBeFalse();
        });

        test('returns empty response when no tuples to delete', function (): void {
            $emptyTuples = new TupleKeys([]);

            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->willReturn([null, null]);

            $this->httpService->expects(test()->never())->method('send');

            $result = $this->repository->delete($this->store, $this->model, $emptyTuples);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap()->getTotalOperations())->toBe(0);
        });

        test('fails when transactional limit is exceeded for deletes', function (): void {
            $tuples = new TupleKeys([]);

            for ($i = 0; 101 > $i; $i++) {
                $tuples->add(new TupleKey("user:user{$i}", 'reader', "document:doc{$i}"));
            }

            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->willReturn([null, $tuples]);

            $result = $this->repository->delete($this->store, $this->model, $tuples);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBeInstanceOf(ClientException::class);
        });

        test('handles filter service exceptions', function (): void {
            $tuples = new TupleKeys([
                new TupleKey('user:alice', 'reader', 'document:old'),
            ]);

            $exception = new RuntimeException('Filter error');
            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->willThrowException($exception);

            $result = $this->repository->delete($this->store, $this->model, $tuples);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBe($exception);
        });
    });

    describe('read()', function (): void {
        test('successfully reads tuples with default parameters', function (): void {
            $filter = new TupleKey('user:alice', '', '');

            $httpResponse = ($this->createHttpResponse)(200, '{"tuples": []}');

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (ReadTuplesRequest $request) use ($filter): bool {
                    expect($request->getStore())->toBe('store-123');
                    expect($request->getTupleKey())->toBe($filter);
                    expect($request->getContinuationToken())->toBeNull();
                    expect($request->getPageSize())->toBeNull();

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->read($this->store, $filter);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(ReadTuplesResponse::class);
        });

        test('successfully reads tuples with pagination', function (): void {
            $filter = new TupleKey('', 'reader', 'document:budget');

            $httpResponse = ($this->createHttpResponse)(200, '{"tuples": [], "continuation_token": "next-page"}');

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (ReadTuplesRequest $request): bool {
                    expect($request->getContinuationToken())->toBe('prev-token');
                    expect($request->getPageSize())->toBe(50);

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->read(
                $this->store,
                $filter,
                continuationToken: 'prev-token',
                pageSize: 50,
            );

            expect($result)->toBeInstanceOf(Success::class);
        });

        test('normalizes page size to allowed range', function (): void {
            $filter = new TupleKey('', '', 'document:budget');

            $httpResponse = ($this->createHttpResponse)(200, '{"tuples": []}');

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            $this->httpService
                ->expects(test()->exactly(3))
                ->method('send')
                ->with(test()->callback(function (ReadTuplesRequest $request): bool {
                    $pageSize = $request->getPageSize();
                    expect($pageSize)->toBeGreaterThanOrEqual(1);
                    expect($pageSize)->toBeLessThanOrEqual(100);

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->exactly(3))
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            // Test minimum boundary
            $this->repository->read($this->store, $filter, pageSize: 0);

            // Test maximum boundary
            $this->repository->read($this->store, $filter, pageSize: 200);

            // Test valid value
            $this->repository->read($this->store, $filter, pageSize: 50);
        });

        test('handles missing last request', function (): void {
            $filter = new TupleKey('user:alice', 'reader', '');

            $httpResponse = test()->createMock(HttpResponseInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn(null);

            $result = $this->repository->read($this->store, $filter);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBeInstanceOf(ClientException::class);
            expect($result->err()->getMessage())->toContain('Failed to capture HTTP request');
        });

        test('handles HTTP service exceptions', function (): void {
            $filter = new TupleKey('user:alice', '', '');

            $exception = NetworkError::Request->exception(context: ['message' => 'Read failed']);
            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willThrowException($exception);

            $result = $this->repository->read($this->store, $filter);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBe($exception);
        });
    });

    describe('listChanges()', function (): void {
        test('successfully lists changes with default parameters', function (): void {
            $httpResponse = ($this->createHttpResponse)(200, '{"changes": []}');

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (ListTupleChangesRequest $request): bool {
                    expect($request->getStore())->toBe('store-123');
                    expect($request->getType())->toBeNull();
                    expect($request->getStartTime())->toBeNull();
                    expect($request->getContinuationToken())->toBeNull();
                    expect($request->getPageSize())->toBeNull();

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->listChanges($this->store);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(ListTupleChangesResponse::class);
        });

        test('successfully lists changes with all parameters', function (): void {
            $startTime = new DateTimeImmutable('-1 day');

            $httpResponse = ($this->createHttpResponse)(200, '{"changes": []}');

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (ListTupleChangesRequest $request) use ($startTime): bool {
                    expect($request->getType())->toBe('document');
                    expect($request->getStartTime())->toBe($startTime);
                    expect($request->getContinuationToken())->toBe('next-page');
                    expect($request->getPageSize())->toBe(75);

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->listChanges(
                $this->store,
                type: 'document',
                startTime: $startTime,
                continuationToken: 'next-page',
                pageSize: 75,
            );

            expect($result)->toBeInstanceOf(Success::class);
        });

        test('normalizes page size for list changes', function (): void {
            $httpResponse = ($this->createHttpResponse)(200, '{"changes": []}');

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            $this->httpService
                ->expects(test()->exactly(3))
                ->method('send')
                ->with(test()->callback(function (ListTupleChangesRequest $request): bool {
                    $pageSize = $request->getPageSize();
                    expect($pageSize)->toBeGreaterThanOrEqual(1);
                    expect($pageSize)->toBeLessThanOrEqual(100);

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->exactly(3))
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            // Test minimum boundary
            $this->repository->listChanges($this->store, pageSize: -5);

            // Test maximum boundary
            $this->repository->listChanges($this->store, pageSize: 500);

            // Test valid value
            $this->repository->listChanges($this->store, pageSize: 25);
        });

        test('handles missing last request for list changes', function (): void {
            $httpResponse = test()->createMock(HttpResponseInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn(null);

            $result = $this->repository->listChanges($this->store);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBeInstanceOf(ClientException::class);
        });

        test('handles service exceptions for list changes', function (): void {
            $exception = SerializationError::Response->exception(context: ['message' => 'Invalid response format']);
            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willThrowException($exception);

            $result = $this->repository->listChanges($this->store, type: 'user');

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBe($exception);
        });
    });

    describe('edge cases', function (): void {
        test('handles large batch operations correctly', function (): void {
            $tuples = new TupleKeys([]);

            for ($i = 0; 250 > $i; $i++) {
                $tuples->add(new TupleKey("user:user{$i}", 'reader', "document:doc{$i}"));
            }

            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->willReturn([$tuples, null]);

            $httpResponse = test()->createMock(HttpResponseInterface::class);
            $httpResponse->method('getStatusCode')->willReturn(200);

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            // Expect 3 chunks (100 + 100 + 50) with default chunk size
            $this->httpService
                ->expects(test()->exactly(3))
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->exactly(3))
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->write(
                $this->store,
                $this->model,
                $tuples,
                transactional: false,
            );

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap()->getTotalOperations())->toBe(250);
            expect($result->unwrap()->getTotalChunks())->toBe(3);
            expect($result->unwrap()->getSuccessfulChunks())->toBe(3);
        });

        test('handles partial failures in non-transactional mode', function (): void {
            $tuples = new TupleKeys([
                new TupleKey('user:alice', 'reader', 'document:1'),
                new TupleKey('user:bob', 'editor', 'document:2'),
                new TupleKey('user:charlie', 'viewer', 'document:3'),
            ]);

            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->willReturn([$tuples, null]);

            $httpResponse = test()->createMock(HttpResponseInterface::class);
            $httpResponse->method('getStatusCode')->willReturn(200);

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            $exception = NetworkError::Request->exception(context: ['message' => 'Chunk 2 failed']);

            // First chunk succeeds, second fails, third succeeds
            $this->httpService
                ->expects(test()->exactly(3))
                ->method('send')
                ->willReturnOnConsecutiveCalls(
                    $httpResponse,
                    test()->throwException($exception),
                    $httpResponse,
                );

            $this->httpService
                ->expects(test()->exactly(2))
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->write(
                $this->store,
                $this->model,
                $tuples,
                transactional: false,
                options: [
                    'maxTuplesPerChunk' => 1,
                    'stopOnFirstError' => false,
                ],
            );

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap()->getTotalChunks())->toBe(3);
            expect($result->unwrap()->getSuccessfulChunks())->toBe(2);
            expect($result->unwrap()->getFailedChunks())->toBe(1);
            expect($result->unwrap()->isPartialSuccess())->toBeTrue();
            expect($result->unwrap()->getErrors())->toHaveCount(1);
            expect($result->unwrap()->getFirstError())->toBe($exception);
        });

        test('handles empty arrays passed as options', function (): void {
            $tuples = new TupleKeys([
                new TupleKey('user:alice', 'reader', 'document:budget'),
            ]);

            $this->tupleFilterService
                ->expects(test()->once())
                ->method('filterDuplicates')
                ->willReturn([$tuples, null]);

            $httpResponse = test()->createMock(HttpResponseInterface::class);
            $httpResponse->method('getStatusCode')->willReturn(200);

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (WriteTuplesRequest $request): bool {
                    // Default values should be used
                    expect($request->getMaxParallelRequests())->toBe(1);
                    expect($request->getMaxTuplesPerChunk())->toBe(100);
                    expect($request->getMaxRetries())->toBe(0);
                    expect($request->getRetryDelaySeconds())->toBe(1.0);
                    expect($request->getStopOnFirstError())->toBeFalse();

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->write(
                $this->store,
                $this->model,
                $tuples,
                transactional: false,
                options: [],
            );

            expect($result)->toBeInstanceOf(Success::class);
        });

        test('handles extremely large page size normalization', function (): void {
            $filter = new TupleKey('', 'admin', '');

            $httpResponse = ($this->createHttpResponse)(200, '{"tuples": []}');

            $httpRequest = test()->createMock(HttpRequestInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (ReadTuplesRequest $request): bool {
                    expect($request->getPageSize())->toBe(100); // Should be capped at 100

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->read(
                $this->store,
                $filter,
                pageSize: PHP_INT_MAX,
            );

            expect($result)->toBeInstanceOf(Success::class);
        });
    });
});
