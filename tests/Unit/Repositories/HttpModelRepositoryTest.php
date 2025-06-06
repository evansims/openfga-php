<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Repositories;

use OpenFGA\Exceptions\ClientError;
use OpenFGA\Models\Collections\{TypeDefinitions};
use OpenFGA\Models\{TypeDefinition};
use OpenFGA\Repositories\HttpModelRepository;
use OpenFGA\Requests\{CreateAuthorizationModelRequest, GetAuthorizationModelRequest, ListAuthorizationModelsRequest};
use OpenFGA\Responses\{CreateAuthorizationModelResponse, GetAuthorizationModelResponse, ListAuthorizationModelsResponse};
use OpenFGA\Results\{Failure, Success};
use OpenFGA\Schemas\SchemaValidator;
use OpenFGA\Services\HttpServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface, StreamInterface};
use RuntimeException;

// Helper function to create a properly formatted HTTP response
function createHttpResponseForModel(int $statusCode, string $body): HttpResponseInterface
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
function createHttpRequestForModel(): HttpRequestInterface
{
    return test()->createMock(HttpRequestInterface::class);
}

beforeEach(function (): void {
    /** @var HttpServiceInterface&MockObject */
    $this->httpService = test()->createMock(HttpServiceInterface::class);

    /** @var SchemaValidator */
    $this->validator = new SchemaValidator;

    $this->storeId = 'test-store-id';
});

describe('HttpModelRepository', function (): void {
    describe('Constructor', function (): void {
        it('constructs with valid store ID', function (): void {
            $repository = new HttpModelRepository(
                $this->httpService,
                $this->validator,
                $this->storeId,
            );

            expect($repository)->toBeInstanceOf(HttpModelRepository::class);
        });

        it('throws exception for empty store ID', function (): void {
            expect(fn () => new HttpModelRepository(
                $this->httpService,
                $this->validator,
                '',
            ))->toThrow(ClientError::Validation->exception()::class);
        });
    });

    describe('create()', function (): void {
        it('creates authorization model successfully', function (): void {
            $typeDefinitions = new TypeDefinitions([
                new TypeDefinition(
                    type: 'user',
                    relations: null,
                ),
            ]);

            $modelId = '01J7P9R5X5QQQQQQQQQQQQQQQQ';

            // Mock the create response
            $createResponse = createHttpResponseForModel(201, json_encode([
                'authorization_model_id' => $modelId,
            ]));

            $mockRequest = createHttpRequestForModel();

            $this->httpService->expects($this->once())
                ->method('send')
                ->with($this->isInstanceOf(CreateAuthorizationModelRequest::class))
                ->willReturn($createResponse);

            $this->httpService->expects($this->once())
                ->method('getLastRequest')
                ->willReturn($mockRequest);

            $repository = new HttpModelRepository(
                $this->httpService,
                $this->validator,
                $this->storeId,
            );

            $result = $repository->create($typeDefinitions);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(CreateAuthorizationModelResponse::class);
            expect($result->unwrap()->getModel())->toBe($modelId);
        });

        it('returns failure on HTTP error', function (): void {
            $typeDefinitions = new TypeDefinitions([
                new TypeDefinition(
                    type: 'user',
                    relations: null,
                ),
            ]);

            $this->httpService->expects($this->once())
                ->method('send')
                ->willThrowException(new RuntimeException('HTTP error'));

            $repository = new HttpModelRepository(
                $this->httpService,
                $this->validator,
                $this->storeId,
            );

            $result = $repository->create($typeDefinitions);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBeInstanceOf(RuntimeException::class);
        });
    });

    describe('get()', function (): void {
        it('retrieves authorization model successfully', function (): void {
            $modelId = '01J7P9R5X5QQQQQQQQQQQQQQQQ';

            $response = createHttpResponseForModel(200, json_encode([
                'authorization_model' => [
                    'id' => $modelId,
                    'schema_version' => '1.1',
                    'type_definitions' => [
                        [
                            'type' => 'user',
                            'relations' => [],
                        ],
                    ],
                ],
            ]));

            $mockRequest = createHttpRequestForModel();

            $this->httpService->expects($this->once())
                ->method('send')
                ->with($this->isInstanceOf(GetAuthorizationModelRequest::class))
                ->willReturn($response);

            $this->httpService->expects($this->once())
                ->method('getLastRequest')
                ->willReturn($mockRequest);

            $repository = new HttpModelRepository(
                $this->httpService,
                $this->validator,
                $this->storeId,
            );

            $result = $repository->get($modelId);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(GetAuthorizationModelResponse::class);
            expect($result->unwrap()->getModel()->getId())->toBe($modelId);
        });

        it('returns failure when model not found (404 response)', function (): void {
            $modelId = '01J7P9R5X5QQQQQQQQQQQQQQQQ';

            $response = createHttpResponseForModel(404, json_encode([
                'code' => 'model_not_found',
                'message' => 'The requested authorization model was not found',
            ]));

            $mockRequest = createHttpRequestForModel();

            $this->httpService->expects($this->once())
                ->method('send')
                ->willReturn($response);

            $this->httpService->expects($this->once())
                ->method('getLastRequest')
                ->willReturn($mockRequest);

            $repository = new HttpModelRepository(
                $this->httpService,
                $this->validator,
                $this->storeId,
            );

            $result = $repository->get($modelId);

            expect($result)->toBeInstanceOf(Failure::class);
        });
    });

    describe('list()', function (): void {
        it('lists authorization models successfully', function (): void {
            $response = createHttpResponseForModel(200, json_encode([
                'authorization_models' => [
                    [
                        'id' => '01J7P9R5X5QQQQQQQQQQQQQQQQ',
                        'schema_version' => '1.1',
                        'type_definitions' => [
                            [
                                'type' => 'user',
                                'relations' => [],
                            ],
                        ],
                    ],
                ],
                'continuation_token' => 'next-page-token',
            ]));

            $mockRequest = createHttpRequestForModel();

            $this->httpService->expects($this->once())
                ->method('send')
                ->with($this->isInstanceOf(ListAuthorizationModelsRequest::class))
                ->willReturn($response);

            $this->httpService->expects($this->once())
                ->method('getLastRequest')
                ->willReturn($mockRequest);

            $repository = new HttpModelRepository(
                $this->httpService,
                $this->validator,
                $this->storeId,
            );

            $result = $repository->list();

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeInstanceOf(ListAuthorizationModelsResponse::class);
            expect($result->unwrap()->getModels()->count())->toBe(1);
        });

        it('normalizes page size correctly', function (): void {
            $response = createHttpResponseForModel(200, json_encode([
                'authorization_models' => [],
            ]));

            $mockRequest = createHttpRequestForModel();

            $this->httpService->expects($this->once())
                ->method('send')
                ->with($this->isInstanceOf(ListAuthorizationModelsRequest::class))
                ->willReturn($response);

            $this->httpService->expects($this->once())
                ->method('getLastRequest')
                ->willReturn($mockRequest);

            $repository = new HttpModelRepository(
                $this->httpService,
                $this->validator,
                $this->storeId,
            );

            // Test with page size > max
            $result = $repository->list(200, null);

            expect($result)->toBeInstanceOf(Success::class);
        });
    });
});
