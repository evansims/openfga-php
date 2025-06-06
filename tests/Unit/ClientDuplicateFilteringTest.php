<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit;

use OpenFGA\Client;
use OpenFGA\Models\Condition;
use OpenFGA\Responses\WriteTuplesResponse;
use OpenFGA\Results\Success;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface, ResponseFactoryInterface, ResponseInterface, StreamFactoryInterface, StreamInterface, UriInterface};

use function OpenFGA\{tuple, tuples};

describe('Client writeTuples duplicate filtering', function (): void {
    beforeEach(function (): void {
        $this->mockHttpClient = test()->createMock(HttpClientInterface::class);
        $this->mockResponseFactory = test()->createMock(ResponseFactoryInterface::class);
        $this->mockStreamFactory = test()->createMock(StreamFactoryInterface::class);
        $this->mockRequestFactory = test()->createMock(RequestFactoryInterface::class);
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

        $this->mockRequest
            ->method('withHeader')
            ->willReturnSelf();

        $this->mockRequest
            ->method('withBody')
            ->willReturnSelf();

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

        $this->mockStream
            ->method('getContents')
            ->willReturn('{}');

        $this->client = new Client(
            url: 'https://api.openfga.dev',
            httpClient: $this->mockHttpClient,
            httpResponseFactory: $this->mockResponseFactory,
            httpStreamFactory: $this->mockStreamFactory,
            httpRequestFactory: $this->mockRequestFactory,
        );
    });

    test('removes duplicate writes', function (): void {
        $writes = tuples(
            tuple('user:anne', 'reader', 'document:1'),
            tuple('user:bob', 'editor', 'document:2'),
            tuple('user:anne', 'reader', 'document:1'), // duplicate
            tuple('user:charlie', 'viewer', 'document:3'),
            tuple('user:bob', 'editor', 'document:2'), // duplicate
        );

        $capturedBody = null;
        $this->mockStreamFactory
            ->expects(test()->once())
            ->method('createStream')
            ->willReturnCallback(function ($body) use (&$capturedBody) {
                $capturedBody = $body;

                return $this->mockStream;
            });

        $this->mockHttpClient
            ->expects(test()->once())
            ->method('sendRequest')
            ->willReturn($this->mockResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
        );

        expect($result)->toBeInstanceOf(Success::class);

        // Verify the request body contains only unique tuples
        $decodedBody = json_decode($capturedBody, true);
        expect($decodedBody['writes']['tuple_keys'])->toHaveCount(3); // Only 3 unique tuples
    });

    test('removes duplicate deletes', function (): void {
        $deletes = tuples(
            tuple('user:anne', 'reader', 'document:1'),
            tuple('user:bob', 'editor', 'document:2'),
            tuple('user:anne', 'reader', 'document:1'), // duplicate
            tuple('user:charlie', 'viewer', 'document:3'),
            tuple('user:bob', 'editor', 'document:2'), // duplicate
        );

        $capturedBody = null;
        $this->mockStreamFactory
            ->expects(test()->once())
            ->method('createStream')
            ->willReturnCallback(function ($body) use (&$capturedBody) {
                $capturedBody = $body;

                return $this->mockStream;
            });

        $this->mockHttpClient
            ->expects(test()->once())
            ->method('sendRequest')
            ->willReturn($this->mockResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            deletes: $deletes,
        );

        expect($result)->toBeInstanceOf(Success::class);

        // Verify the request body contains only unique tuples
        $decodedBody = json_decode($capturedBody, true);
        expect($decodedBody['deletes']['tuple_keys'])->toHaveCount(3); // Only 3 unique tuples
    });

    test('delete takes precedence when tuple appears in both writes and deletes', function (): void {
        $writes = tuples(
            tuple('user:anne', 'reader', 'document:1'),
            tuple('user:bob', 'editor', 'document:2'),
            tuple('user:charlie', 'viewer', 'document:3'),
        );

        $deletes = tuples(
            tuple('user:bob', 'editor', 'document:2'), // This should remove bob from writes
            tuple('user:david', 'owner', 'document:4'),
        );

        $capturedBody = null;
        $this->mockStreamFactory
            ->expects(test()->once())
            ->method('createStream')
            ->willReturnCallback(function ($body) use (&$capturedBody) {
                $capturedBody = $body;

                return $this->mockStream;
            });

        $this->mockHttpClient
            ->expects(test()->once())
            ->method('sendRequest')
            ->willReturn($this->mockResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
            deletes: $deletes,
        );

        expect($result)->toBeInstanceOf(Success::class);

        // Verify the request body
        $decodedBody = json_decode($capturedBody, true);
        expect($decodedBody['writes']['tuple_keys'])->toHaveCount(2); // Bob should be removed
        expect($decodedBody['deletes']['tuple_keys'])->toHaveCount(2); // Both deletes remain

        // Verify bob is not in writes
        $writeUsers = array_column($decodedBody['writes']['tuple_keys'], 'user');
        expect($writeUsers)->not->toContain('user:bob');

        // Verify bob is in deletes
        $deleteUsers = array_column($decodedBody['deletes']['tuple_keys'], 'user');
        expect($deleteUsers)->toContain('user:bob');
    });

    test('handles empty writes and deletes', function (): void {
        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: null,
            deletes: null,
        );

        expect($result)->toBeInstanceOf(Success::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->getTotalOperations())->toBe(0);
    });

    test('preserves order of first occurrence when filtering duplicates', function (): void {
        $writes = tuples(
            tuple('user:anne', 'reader', 'document:1'),
            tuple('user:bob', 'editor', 'document:2'),
            tuple('user:charlie', 'viewer', 'document:3'),
            tuple('user:anne', 'reader', 'document:1'), // duplicate - should be ignored
            tuple('user:david', 'owner', 'document:4'),
        );

        $capturedBody = null;
        $this->mockStreamFactory
            ->expects(test()->once())
            ->method('createStream')
            ->willReturnCallback(function ($body) use (&$capturedBody) {
                $capturedBody = $body;

                return $this->mockStream;
            });

        $this->mockHttpClient
            ->expects(test()->once())
            ->method('sendRequest')
            ->willReturn($this->mockResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
        );

        expect($result)->toBeInstanceOf(Success::class);

        // Verify order is preserved
        $decodedBody = json_decode($capturedBody, true);
        expect($decodedBody['writes']['tuple_keys'][0]['user'])->toBe('user:anne');
        expect($decodedBody['writes']['tuple_keys'][1]['user'])->toBe('user:bob');
        expect($decodedBody['writes']['tuple_keys'][2]['user'])->toBe('user:charlie');
        expect($decodedBody['writes']['tuple_keys'][3]['user'])->toBe('user:david');
    });

    test('handles tuples with conditions correctly', function (): void {
        $condition1 = new Condition('condition1', 'ip == "192.168.1.1"');
        $condition2 = new Condition('condition2', 'ip == "192.168.1.2"');

        $writes = tuples(
            tuple('user:anne', 'reader', 'document:1', $condition1),
            tuple('user:anne', 'reader', 'document:1', $condition2), // Same tuple but different condition
            tuple('user:anne', 'reader', 'document:1', $condition1), // Duplicate with same condition
        );

        $capturedBody = null;
        $this->mockStreamFactory
            ->expects(test()->once())
            ->method('createStream')
            ->willReturnCallback(function ($body) use (&$capturedBody) {
                $capturedBody = $body;

                return $this->mockStream;
            });

        $this->mockHttpClient
            ->expects(test()->once())
            ->method('sendRequest')
            ->willReturn($this->mockResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
        );

        expect($result)->toBeInstanceOf(Success::class);

        // Verify we have 2 tuples (different conditions make them unique)
        $decodedBody = json_decode($capturedBody, true);
        expect($decodedBody['writes']['tuple_keys'])->toHaveCount(2);
    });

    test('complex scenario with mixed duplicates and overlaps', function (): void {
        $writes = tuples(
            tuple('user:anne', 'reader', 'document:1'),
            tuple('user:bob', 'editor', 'document:2'),
            tuple('user:charlie', 'viewer', 'document:3'),
            tuple('user:anne', 'reader', 'document:1'), // duplicate write
            tuple('user:david', 'owner', 'document:4'),
            tuple('user:eve', 'reader', 'document:5'),
            tuple('user:bob', 'editor', 'document:2'), // duplicate write
        );

        $deletes = tuples(
            tuple('user:charlie', 'viewer', 'document:3'), // overlaps with writes
            tuple('user:frank', 'editor', 'document:6'),
            tuple('user:eve', 'reader', 'document:5'), // overlaps with writes
            tuple('user:frank', 'editor', 'document:6'), // duplicate delete
            tuple('user:gary', 'owner', 'document:7'),
        );

        $capturedBody = null;
        $this->mockStreamFactory
            ->expects(test()->once())
            ->method('createStream')
            ->willReturnCallback(function ($body) use (&$capturedBody) {
                $capturedBody = $body;

                return $this->mockStream;
            });

        $this->mockHttpClient
            ->expects(test()->once())
            ->method('sendRequest')
            ->willReturn($this->mockResponse);

        $result = $this->client->writeTuples(
            store: 'store-id',
            model: 'model-id',
            writes: $writes,
            deletes: $deletes,
        );

        expect($result)->toBeInstanceOf(Success::class);

        $decodedBody = json_decode($capturedBody, true);

        // Writes should have: anne, bob, david (charlie and eve removed due to deletes)
        expect($decodedBody['writes']['tuple_keys'])->toHaveCount(3);
        $writeUsers = array_column($decodedBody['writes']['tuple_keys'], 'user');
        expect($writeUsers)->toContain('user:anne');
        expect($writeUsers)->toContain('user:bob');
        expect($writeUsers)->toContain('user:david');
        expect($writeUsers)->not->toContain('user:charlie');
        expect($writeUsers)->not->toContain('user:eve');

        // Deletes should have: charlie, frank, eve, gary (no duplicates)
        expect($decodedBody['deletes']['tuple_keys'])->toHaveCount(4);
        $deleteUsers = array_column($decodedBody['deletes']['tuple_keys'], 'user');
        expect($deleteUsers)->toContain('user:charlie');
        expect($deleteUsers)->toContain('user:frank');
        expect($deleteUsers)->toContain('user:eve');
        expect($deleteUsers)->toContain('user:gary');
    });
});
