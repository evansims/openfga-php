<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Repositories;

use DateTimeImmutable;
use DateTimeInterface;
use OpenFGA\Exceptions\{ClientException, NetworkError, NetworkException};
use OpenFGA\Models\Collections\{Stores};
use OpenFGA\Models\{StoreInterface};
use OpenFGA\Repositories\HttpStoreRepository;
use OpenFGA\Requests\{CreateStoreRequest, DeleteStoreRequest, GetStoreRequest, ListStoresRequest};
use OpenFGA\Responses\{GetStoreResponse, ListStoresResponse};
use OpenFGA\Results\{Failure, Success};
use OpenFGA\Schemas\SchemaValidator;
use OpenFGA\Services\HttpServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface, StreamInterface};
use RuntimeException;

// Helper function to create a properly formatted HTTP response
function createHttpResponseForStore(int $statusCode, string $body): HttpResponseInterface
{
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('getContents')->willReturn($body);
    $stream->method('__toString')->willReturn($body);

    $response = test()->createMock(HttpResponseInterface::class);
    $response->method('getStatusCode')->willReturn($statusCode);
    $response->method('getBody')->willReturn($stream);

    return $response;
}

// Helper function to create a mock HTTP request
function createHttpRequestForStore(): HttpRequestInterface
{
    return test()->createMock(HttpRequestInterface::class);
}

describe('HttpStoreRepository', function (): void {
    beforeEach(function (): void {
        /** @var HttpServiceInterface&MockObject */
        $this->httpService = test()->createMock(HttpServiceInterface::class);

        /** @var SchemaValidator */
        $this->validator = new SchemaValidator;

        $this->repository = new HttpStoreRepository(
            $this->httpService,
            $this->validator,
        );
    });

    describe('create()', function (): void {
        test('successfully creates a store with valid name', function (): void {
            $storeName = 'My Test Store';
            $storeId = 'store-12345';
            $createdAt = new DateTimeImmutable('2024-01-01T00:00:00Z');
            $updatedAt = new DateTimeImmutable('2024-01-01T00:00:00Z');

            $responseBody = json_encode([
                'id' => $storeId,
                'name' => $storeName,
                'created_at' => $createdAt->format(DateTimeInterface::RFC3339_EXTENDED),
                'updated_at' => $updatedAt->format(DateTimeInterface::RFC3339_EXTENDED),
            ]);

            $httpResponse = createHttpResponseForStore(201, $responseBody);
            $httpRequest = createHttpRequestForStore();

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (CreateStoreRequest $request) use ($storeName): bool {
                    expect($request->getName())->toBe($storeName);

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->create($storeName);

            expect($result)->toBeInstanceOf(Success::class);

            $store = $result->unwrap();
            expect($store)->toBeInstanceOf(StoreInterface::class);
            expect($store->getId())->toBe($storeId);
            expect($store->getName())->toBe($storeName);
            expect($store->getCreatedAt()->format(DateTimeInterface::RFC3339_EXTENDED))
                ->toBe($createdAt->format(DateTimeInterface::RFC3339_EXTENDED));
        });

        test('trims whitespace from store name', function (): void {
            $storeNameWithWhitespace = '  Trimmed Store Name  ';
            $trimmedName = 'Trimmed Store Name';
            $storeId = 'store-67890';

            $responseBody = json_encode([
                'id' => $storeId,
                'name' => $trimmedName,
                'created_at' => '2024-01-01T00:00:00.000Z',
                'updated_at' => '2024-01-01T00:00:00.000Z',
            ]);

            $httpResponse = createHttpResponseForStore(201, $responseBody);
            $httpRequest = createHttpRequestForStore();

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (CreateStoreRequest $request) use ($trimmedName): bool {
                    expect($request->getName())->toBe($trimmedName);

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->create($storeNameWithWhitespace);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap()->getName())->toBe($trimmedName);
        });

        test('returns failure for empty store name', function (): void {
            $this->httpService
                ->expects(test()->never())
                ->method('send');

            $result = $this->repository->create('');

            expect($result)->toBeInstanceOf(Failure::class);

            $error = $result->err();
            expect($error)->toBeInstanceOf(ClientException::class);
            expect($error->getMessage())->toContain('Store name cannot be empty');
        });

        test('returns failure for whitespace-only store name', function (): void {
            $this->httpService
                ->expects(test()->never())
                ->method('send');

            $result = $this->repository->create('   ');

            expect($result)->toBeInstanceOf(Failure::class);

            $error = $result->err();
            expect($error)->toBeInstanceOf(ClientException::class);
            expect($error->getMessage())->toContain('Store name cannot be empty');
        });

        test('handles very long store names', function (): void {
            $longStoreName = str_repeat('a', 1000);
            $storeId = 'store-long-name';

            $responseBody = json_encode([
                'id' => $storeId,
                'name' => $longStoreName,
                'created_at' => '2024-01-01T00:00:00.000Z',
                'updated_at' => '2024-01-01T00:00:00.000Z',
            ]);

            $httpResponse = createHttpResponseForStore(201, $responseBody);
            $httpRequest = createHttpRequestForStore();

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->create($longStoreName);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap()->getName())->toBe($longStoreName);
        });

        test('handles HTTP errors during creation', function (): void {
            $storeName = 'Test Store';

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willThrowException(NetworkError::Unexpected->exception());

            $result = $this->repository->create($storeName);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBeInstanceOf(NetworkException::class);
        });

        test('handles missing last request', function (): void {
            $storeName = 'Test Store';
            $httpResponse = createHttpResponseForStore(201, '{}');

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn(null);

            $result = $this->repository->create($storeName);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBeInstanceOf(ClientException::class);
            expect($result->err()->getMessage())->toContain('Failed to capture HTTP request');
        });

        test('handles invalid response JSON', function (): void {
            $storeName = 'Test Store';
            $httpResponse = createHttpResponseForStore(201, 'invalid json');
            $httpRequest = createHttpRequestForStore();

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->create($storeName);

            expect($result)->toBeInstanceOf(Failure::class);
        });
    });

    describe('get()', function (): void {
        test('successfully retrieves a store by ID', function (): void {
            $storeId = 'store-12345';
            $storeName = 'Retrieved Store';
            $createdAt = new DateTimeImmutable('2024-01-01T00:00:00Z');
            $updatedAt = new DateTimeImmutable('2024-01-02T00:00:00Z');

            $responseBody = json_encode([
                'id' => $storeId,
                'name' => $storeName,
                'created_at' => $createdAt->format(DateTimeInterface::RFC3339_EXTENDED),
                'updated_at' => $updatedAt->format(DateTimeInterface::RFC3339_EXTENDED),
            ]);

            $httpResponse = createHttpResponseForStore(200, $responseBody);
            $httpRequest = createHttpRequestForStore();

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (GetStoreRequest $request) use ($storeId): bool {
                    expect($request->getStore())->toBe($storeId);

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->get($storeId);

            expect($result)->toBeInstanceOf(Success::class);

            $response = $result->unwrap();
            expect($response)->toBeInstanceOf(GetStoreResponse::class);
            expect($response->getId())->toBe($storeId);
            expect($response->getName())->toBe($storeName);
        });

        test('handles store not found error', function (): void {
            $storeId = 'non-existent-store';
            $errorBody = json_encode([
                'code' => 'store_not_found',
                'message' => 'Store not found',
            ]);

            $httpResponse = createHttpResponseForStore(404, $errorBody);
            $httpRequest = createHttpRequestForStore();

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->get($storeId);

            expect($result)->toBeInstanceOf(Failure::class);
        });

        test('handles network errors during retrieval', function (): void {
            $storeId = 'store-12345';

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willThrowException(NetworkError::Timeout->exception());

            $result = $this->repository->get($storeId);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBeInstanceOf(NetworkException::class);
        });

        test('handles missing last request for get', function (): void {
            $storeId = 'store-12345';
            $httpResponse = createHttpResponseForStore(200, '{}');

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn(null);

            $result = $this->repository->get($storeId);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err()->getMessage())->toContain('Failed to capture HTTP request');
        });
    });

    describe('list()', function (): void {
        test('successfully lists stores without pagination', function (): void {
            $stores = [
                [
                    'id' => 'store-1',
                    'name' => 'Store One',
                    'created_at' => '2024-01-01T00:00:00.000Z',
                    'updated_at' => '2024-01-01T00:00:00.000Z',
                ],
                [
                    'id' => 'store-2',
                    'name' => 'Store Two',
                    'created_at' => '2024-01-02T00:00:00.000Z',
                    'updated_at' => '2024-01-02T00:00:00.000Z',
                ],
            ];

            $responseBody = json_encode([
                'stores' => $stores,
                'continuation_token' => '',
            ]);

            $httpResponse = createHttpResponseForStore(200, $responseBody);
            $httpRequest = createHttpRequestForStore();

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (ListStoresRequest $request): bool {
                    expect($request->getContinuationToken())->toBeNull();
                    expect($request->getPageSize())->toBeNull();

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->list();

            expect($result)->toBeInstanceOf(Success::class);

            $response = $result->unwrap();
            expect($response)->toBeInstanceOf(ListStoresResponse::class);
            expect($response->getStores()->count())->toBe(2);
            expect($response->getStores()->get(0)->getId())->toBe('store-1');
            expect($response->getStores()->get(1)->getId())->toBe('store-2');
        });

        test('successfully lists stores with pagination', function (): void {
            $continuationToken = 'next-page-token';
            $pageSize = 50;

            $stores = [
                [
                    'id' => 'store-3',
                    'name' => 'Store Three',
                    'created_at' => '2024-01-03T00:00:00.000Z',
                    'updated_at' => '2024-01-03T00:00:00.000Z',
                ],
            ];

            $responseBody = json_encode([
                'stores' => $stores,
                'continuation_token' => 'another-token',
            ]);

            $httpResponse = createHttpResponseForStore(200, $responseBody);
            $httpRequest = createHttpRequestForStore();

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (ListStoresRequest $request) use ($continuationToken, $pageSize): bool {
                    expect($request->getContinuationToken())->toBe($continuationToken);
                    expect($request->getPageSize())->toBe($pageSize);

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->list($continuationToken, $pageSize);

            expect($result)->toBeInstanceOf(Success::class);

            $response = $result->unwrap();
            expect($response->getStores()->count())->toBe(1);
            // Note: continuation token is part of ListStoresResponse, not the Stores collection itself
        });

        test('normalizes page size to minimum bound', function (): void {
            $pageSize = 0; // Should be normalized to 1

            $responseBody = json_encode([
                'stores' => [],
                'continuation_token' => '',
            ]);

            $httpResponse = createHttpResponseForStore(200, $responseBody);
            $httpRequest = createHttpRequestForStore();

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (ListStoresRequest $request): bool {
                    expect($request->getPageSize())->toBe(1);
                    expect($request->getContinuationToken())->toBeNull();

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->list(null, $pageSize);

            expect($result)->toBeInstanceOf(Success::class);
        });

        test('normalizes page size to maximum bound', function (): void {
            $pageSize = 200; // Should be normalized to 100

            $responseBody = json_encode([
                'stores' => [],
                'continuation_token' => '',
            ]);

            $httpResponse = createHttpResponseForStore(200, $responseBody);
            $httpRequest = createHttpRequestForStore();

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (ListStoresRequest $request): bool {
                    expect($request->getPageSize())->toBe(100);
                    expect($request->getContinuationToken())->toBeNull();

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->list(null, $pageSize);

            expect($result)->toBeInstanceOf(Success::class);
        });

        test('handles empty store list', function (): void {
            $responseBody = json_encode([
                'stores' => [],
                'continuation_token' => '',
            ]);

            $httpResponse = createHttpResponseForStore(200, $responseBody);
            $httpRequest = createHttpRequestForStore();

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->list();

            expect($result)->toBeInstanceOf(Success::class);

            $response = $result->unwrap();
            expect($response->getStores()->count())->toBe(0);
            // The Stores collection itself doesn't have getContinuationToken()
        });

        test('handles network errors during listing', function (): void {
            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willThrowException(NetworkError::Unexpected->exception());

            $result = $this->repository->list();

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBeInstanceOf(NetworkException::class);
        });

        test('handles missing last request for list', function (): void {
            $httpResponse = createHttpResponseForStore(200, '{"stores": []}');

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn(null);

            $result = $this->repository->list();

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err()->getMessage())->toContain('Failed to capture HTTP request');
        });
    });

    describe('delete()', function (): void {
        test('successfully deletes a store', function (): void {
            $storeId = 'store-to-delete';

            $httpResponse = createHttpResponseForStore(204, '');
            $httpRequest = createHttpRequestForStore();

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(function (DeleteStoreRequest $request) use ($storeId): bool {
                    expect($request->getStore())->toBe($storeId);

                    return true;
                }))
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->delete($storeId);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeNull();
        });

        test('handles store not found during deletion', function (): void {
            $storeId = 'non-existent-store';
            $errorBody = json_encode([
                'code' => 'store_not_found',
                'message' => 'Store not found',
            ]);

            $httpResponse = createHttpResponseForStore(404, $errorBody);
            $httpRequest = createHttpRequestForStore();

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->delete($storeId);

            expect($result)->toBeInstanceOf(Failure::class);
        });

        test('handles network errors during deletion', function (): void {
            $storeId = 'store-12345';

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willThrowException(NetworkError::Timeout->exception());

            $result = $this->repository->delete($storeId);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBeInstanceOf(NetworkException::class);
        });

        test('handles missing last request for delete', function (): void {
            $storeId = 'store-12345';
            $httpResponse = createHttpResponseForStore(204, '');

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn(null);

            $result = $this->repository->delete($storeId);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err()->getMessage())->toContain('Failed to capture HTTP request');
        });

        test('handles invalid store ID format', function (): void {
            $invalidStoreId = 'store with spaces';

            $httpResponse = createHttpResponseForStore(400, json_encode([
                'code' => 'invalid_request',
                'message' => 'Invalid store ID format',
            ]));
            $httpRequest = createHttpRequestForStore();

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($httpResponse);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn($httpRequest);

            $result = $this->repository->delete($invalidStoreId);

            expect($result)->toBeInstanceOf(Failure::class);
        });
    });

    describe('dependency injection', function (): void {
        test('requires HttpServiceInterface and SchemaValidator', function (): void {
            $httpService = test()->createMock(HttpServiceInterface::class);
            $validator = new SchemaValidator;

            $repository = new HttpStoreRepository($httpService, $validator);

            expect($repository)->toBeInstanceOf(HttpStoreRepository::class);
        });
    });

    describe('error handling consistency', function (): void {
        test('all methods return Failure on exception', function (): void {
            $exception = new RuntimeException('Unexpected error');

            $this->httpService
                ->method('send')
                ->willThrowException($exception);

            // Test create
            $createResult = $this->repository->create('Test Store');
            expect($createResult)->toBeInstanceOf(Failure::class);
            expect($createResult->err())->toBe($exception);

            // Test get
            $getResult = $this->repository->get('store-123');
            expect($getResult)->toBeInstanceOf(Failure::class);
            expect($getResult->err())->toBe($exception);

            // Test list
            $listResult = $this->repository->list();
            expect($listResult)->toBeInstanceOf(Failure::class);
            expect($listResult->err())->toBe($exception);

            // Test delete
            $deleteResult = $this->repository->delete('store-123');
            expect($deleteResult)->toBeInstanceOf(Failure::class);
            expect($deleteResult->err())->toBe($exception);
        });
    });
});
