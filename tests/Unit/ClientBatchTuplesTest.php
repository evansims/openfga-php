<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit;

use OpenFGA\Client;
use OpenFGA\Exceptions\NetworkException;
use OpenFGA\Models\Collections\TupleKeys;
use OpenFGA\Observability\TelemetryInterface;
use OpenFGA\Responses\WriteTuplesResponse;
use OpenFGA\Results\{Failure, Success};
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface, ResponseFactoryInterface, ResponseInterface, StreamFactoryInterface, StreamInterface, UriInterface};
use RuntimeException;

use function OpenFGA\{tuple, tuples};

describe('Client writeTuples Non-Transactional Unit Tests', function (): void {
    beforeEach(function (): void {
        $this->mockHttpClient = test()->createMock(HttpClientInterface::class);
        $this->mockResponseFactory = test()->createMock(ResponseFactoryInterface::class);
        $this->mockStreamFactory = test()->createMock(StreamFactoryInterface::class);
        $this->mockRequestFactory = test()->createMock(RequestFactoryInterface::class);
        $this->mockTelemetry = test()->createMock(TelemetryInterface::class);
        $this->mockResponse = test()->createMock(ResponseInterface::class);
        $this->mockRequest = test()->createMock(RequestInterface::class);
        $this->mockStream = test()->createMock(StreamInterface::class);
        $this->mockUri = test()->createMock(UriInterface::class);

        // Setup basic factory mocks
        $this->mockRequestFactory
            ->method('createRequest')
            ->willReturn($this->mockRequest);

        $this->mockStreamFactory
            ->method('createStream')
            ->willReturn($this->mockStream);

        $this->mockStream
            ->method('getSize')
            ->willReturn(100);

        $this->mockRequest
            ->method('withHeader')
            ->willReturnSelf();

        $this->mockRequest
            ->method('withBody')
            ->willReturnSelf();

        $this->mockRequest
            ->method('getBody')
            ->willReturn($this->mockStream);

        $this->mockRequest
            ->method('getUri')
            ->willReturn($this->mockUri);

        $this->mockUri
            ->method('__toString')
            ->willReturn('https://api.openfga.dev/stores/store-id/write');

        $this->mockResponse
            ->method('getStatusCode')
            ->willReturn(200);

        $this->mockResponse
            ->method('getBody')
            ->willReturn($this->mockStream);

        $this->client = Client::create(
            url: 'https://api.openfga.dev',
            httpClient: $this->mockHttpClient,
            httpResponseFactory: $this->mockResponseFactory,
            httpStreamFactory: $this->mockStreamFactory,
            httpRequestFactory: $this->mockRequestFactory,
            telemetry: $this->mockTelemetry,
            httpMaxRetries: 3, // Use default HTTP-level retries like the working test
        );
    });

    test('returns success for empty operation', function (): void {
        $writes = new TupleKeys([]);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
            transactional: false,
        );

        expect($result)->toBeInstanceOf(Success::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->getTotalOperations())->toBe(0);
        expect($response->getTotalChunks())->toBe(0);
        expect($response->getSuccessfulChunks())->toBe(0);
        expect($response->getFailedChunks())->toBe(0);
    });

    test('processes single chunk sequentially', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'reader', 'document:2'),
        );

        $this->mockHttpClient
            ->expects(test()->once())
            ->method('sendRequest')
            ->willReturn($this->mockResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
            transactional: false,
            maxParallelRequests: 1,
            maxTuplesPerChunk: 10,
        );

        expect($result)->toBeInstanceOf(Success::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->isTransactional())->toBe(false);
        expect($response->getTotalOperations())->toBe(2);
        expect($response->getTotalChunks())->toBe(1);
        expect($response->getSuccessfulChunks())->toBe(1);
        expect($response->getFailedChunks())->toBe(0);
    });

    test('processes multiple chunks sequentially', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'reader', 'document:2'),
            tuple('user:charlie', 'reader', 'document:3'),
        );

        $this->mockHttpClient
            ->expects(test()->exactly(2)) // 3 tuples split into 2 chunks (size 2)
            ->method('sendRequest')
            ->willReturn($this->mockResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
            transactional: false,
            maxParallelRequests: 1,
            maxTuplesPerChunk: 2, // Force chunking
        );

        expect($result)->toBeInstanceOf(Success::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->isTransactional())->toBe(false);
        expect($response->getTotalOperations())->toBe(3);
        expect($response->getTotalChunks())->toBe(2);
        expect($response->getSuccessfulChunks())->toBe(2);
        expect($response->getFailedChunks())->toBe(0);
    });

    test('validates and normalizes parameters', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
        );

        $this->mockHttpClient
            ->expects(test()->once())
            ->method('sendRequest')
            ->willReturn($this->mockResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
            transactional: false,
            maxParallelRequests: -1,     // Should be normalized to 1
            maxTuplesPerChunk: 200,      // Should be normalized to 100
            maxRetries: -1,              // Should be normalized to 0
            retryDelaySeconds: -5.0,     // Should be normalized to 0.0
        );

        expect($result)->toBeInstanceOf(Success::class);
    });

    test('handles chunk processing failure in sequential mode', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
        );

        $failureResponse = test()->createMock(ResponseInterface::class);
        $failureResponse->method('getStatusCode')->willReturn(500);
        $failureResponse->method('getBody')->willReturn($this->mockStream);
        $this->mockStream->method('getContents')->willReturn('{"error": "Internal Server Error"}');

        $this->mockHttpClient
            ->expects(test()->exactly(2)) // Will try twice with maxRetries: 1
            ->method('sendRequest')
            ->willReturn($failureResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
            transactional: false,
            maxRetries: 1, // Allow one retry to see if behavior changes
        );

        expect($result)->toBeInstanceOf(Success::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->isTransactional())->toBe(false);
        expect($response->getTotalOperations())->toBe(1);
        expect($response->getTotalChunks())->toBe(1);
        expect($response->getSuccessfulChunks())->toBe(0);
        expect($response->getFailedChunks())->toBe(1);
        expect($response->getErrors())->toHaveCount(1);
    });

    test('implements retry logic for failed chunks', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
        );

        $failureResponse = test()->createMock(ResponseInterface::class);
        $failureResponse->method('getStatusCode')->willReturn(429); // Rate limited
        $failureResponse->method('getBody')->willReturn($this->mockStream);
        $this->mockStream->method('getContents')->willReturn('{"error": "Rate limited"}');

        $successResponse = test()->createMock(ResponseInterface::class);
        $successResponse->method('getStatusCode')->willReturn(200);
        $successResponse->method('getBody')->willReturn($this->mockStream);

        $this->mockHttpClient
            ->expects(test()->exactly(2)) // 1 initial + 1 retry
            ->method('sendRequest')
            ->willReturnOnConsecutiveCalls($failureResponse, $successResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
            transactional: false,
            maxRetries: 1,
            retryDelaySeconds: 0.001, // Very short delay for testing
        );

        expect($result)->toBeInstanceOf(Success::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->getTotalChunks())->toBe(1);
        expect($response->getSuccessfulChunks())->toBe(1);
        expect($response->getFailedChunks())->toBe(0);
    });

    test('stops on first error when configured', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'reader', 'document:2'),
            tuple('user:charlie', 'reader', 'document:3'),
        );

        $failureResponse = test()->createMock(ResponseInterface::class);
        $failureResponse->method('getStatusCode')->willReturn(500);
        $failureResponse->method('getBody')->willReturn($this->mockStream);
        $this->mockStream->method('getContents')->willReturn('{"error": "Internal Server Error"}');

        $this->mockHttpClient
            ->expects(test()->once()) // Should stop after first failure
            ->method('sendRequest')
            ->willReturn($failureResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
            transactional: false,
            maxParallelRequests: 1,
            maxTuplesPerChunk: 1, // 3 chunks
            stopOnFirstError: true,
        );

        expect($result)->toBeInstanceOf(Success::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->getTotalOperations())->toBe(3);
        expect($response->getTotalChunks())->toBe(3);
        expect($response->getSuccessfulChunks())->toBe(0);
        expect($response->getFailedChunks())->toBe(1);
        expect($response->getErrors())->toHaveCount(1);
    });

    test('continues processing when stopOnFirstError is false', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'reader', 'document:2'),
        );

        $failureResponse = test()->createMock(ResponseInterface::class);
        $failureResponse->method('getStatusCode')->willReturn(500);
        $failureResponse->method('getBody')->willReturn($this->mockStream);

        $successResponse = test()->createMock(ResponseInterface::class);
        $successResponse->method('getStatusCode')->willReturn(200);
        $successResponse->method('getBody')->willReturn($this->mockStream);

        $this->mockHttpClient
            ->expects(test()->exactly(2)) // Should process both chunks
            ->method('sendRequest')
            ->willReturnOnConsecutiveCalls($failureResponse, $successResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
            transactional: false,
            maxParallelRequests: 1,
            maxTuplesPerChunk: 1, // 2 chunks
            stopOnFirstError: false,
        );

        expect($result)->toBeInstanceOf(Success::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->getTotalChunks())->toBe(2);
        expect($response->getSuccessfulChunks())->toBe(1);
        expect($response->getFailedChunks())->toBe(1);
    });

    test('handles exceptions in sequential processing', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
        );

        $this->mockHttpClient
            ->expects(test()->once())
            ->method('sendRequest')
            ->willThrowException(new RuntimeException('Connection failed'));

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
            transactional: false,
            maxRetries: 0,
        );

        expect($result)->toBeInstanceOf(Success::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->getTotalChunks())->toBe(1);
        expect($response->getSuccessfulChunks())->toBe(0);
        expect($response->getFailedChunks())->toBe(1);
        expect($response->getErrors())->toHaveCount(1);
        // NetworkException wraps the original RuntimeException
        expect($response->getErrors()[0])->toBeInstanceOf(NetworkException::class);
    });

    test('telemetry integration works correctly', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
        );

        $this->mockHttpClient
            ->method('sendRequest')
            ->willReturn($this->mockResponse);

        // Note: writeTuples bypasses the service layer and goes directly to the repository,
        // so it doesn't call startOperation directly. Telemetry happens at the HTTP level.
        $this->mockTelemetry
            ->expects(test()->never())
            ->method('startOperation');

        $result = $this->client->writeTuples('store-id', 'model-id', $writes, transactional: false);

        expect($result)->toBeInstanceOf(Success::class);
    });

    test('uses parallel processing when maxParallelRequests > 1', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'reader', 'document:2'),
            tuple('user:charlie', 'reader', 'document:3'),
            tuple('user:david', 'reader', 'document:4'),
        );

        $this->mockHttpClient
            ->expects(test()->exactly(2)) // 4 tuples split into 2 chunks
            ->method('sendRequest')
            ->willReturn($this->mockResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
            transactional: false,
            maxParallelRequests: 2,  // Should trigger parallel processing
            maxTuplesPerChunk: 2,
        );

        expect($result)->toBeInstanceOf(Success::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->getTotalOperations())->toBe(4);
        expect($response->getTotalChunks())->toBe(2);
        expect($response->getSuccessfulChunks())->toBe(2);
        expect($response->getFailedChunks())->toBe(0);
    });

    test('handles mixed writes and deletes correctly', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
        );

        $deletes = tuples(
            tuple('user:bob', 'reader', 'document:2'),
            tuple('user:charlie', 'reader', 'document:3'),
        );

        $this->mockHttpClient
            ->expects(test()->once()) // All 3 operations fit in one chunk
            ->method('sendRequest')
            ->willReturn($this->mockResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
            deletes: $deletes,
            transactional: false,
            maxTuplesPerChunk: 10,
        );

        expect($result)->toBeInstanceOf(Success::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->getTotalOperations())->toBe(3);
        expect($response->getTotalChunks())->toBe(1);
        expect($response->getSuccessfulChunks())->toBe(1);
        expect($response->getFailedChunks())->toBe(0);
    });
});
