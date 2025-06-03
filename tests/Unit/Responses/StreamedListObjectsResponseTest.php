<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Responses;

use Generator;
use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Responses\StreamedListObjectsResponse;
use OpenFGA\Schema\SchemaValidator;
use OpenFGA\Tests\Support\Responses\SimpleStream;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

describe('StreamedListObjectsResponse', function (): void {
    test('creates response with object identifier', function (): void {
        $response = new StreamedListObjectsResponse('document:budget-2024');

        expect($response->getObject())->toBe('document:budget-2024');
    });

    test('processes streaming response successfully', function (): void {
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        // Create streaming data with multiple objects
        $streamData = implode("\n", [
            '{"result":{"object":"document:budget-2024"}}',
            '{"result":{"object":"document:proposal-2024"}}',
            '{"result":{"object":"document:report-2024"}}',
            '',
        ]);

        $stream = new SimpleStream($streamData);
        $response = test()->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);

        $generator = StreamedListObjectsResponse::fromResponse($response, $request, $validator);

        expect($generator)->toBeInstanceOf(Generator::class);

        $results = [];

        foreach ($generator as $streamedResponse) {
            expect($streamedResponse)->toBeInstanceOf(StreamedListObjectsResponse::class);
            $results[] = $streamedResponse->getObject();
        }

        expect($results)->toBe([
            'document:budget-2024',
            'document:proposal-2024',
            'document:report-2024',
        ]);
    });

    test('handles empty lines in stream', function (): void {
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        $streamData = implode("\n", [
            '{"result":{"object":"document:budget-2024"}}',
            '',
            '  ',
            '{"result":{"object":"document:proposal-2024"}}',
            '',
        ]);

        $stream = new SimpleStream($streamData);
        $response = test()->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);

        $generator = StreamedListObjectsResponse::fromResponse($response, $request, $validator);

        $results = [];

        foreach ($generator as $streamedResponse) {
            $results[] = $streamedResponse->getObject();
        }

        // Should skip empty/whitespace lines
        expect($results)->toBe([
            'document:budget-2024',
            'document:proposal-2024',
        ]);
    });

    test('throws SerializationException for invalid JSON', function (): void {
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        $streamData = implode("\n", [
            '{"result":{"object":"document:budget-2024"}}',
            '{invalid json}',
            '',
        ]);

        $stream = new SimpleStream($streamData);
        $response = test()->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);

        $generator = StreamedListObjectsResponse::fromResponse($response, $request, $validator);

        expect(function () use ($generator): void {
            foreach ($generator as $streamedResponse) {
                // Should throw on invalid JSON during iteration
            }
        })->toThrow(SerializationException::class);
    });

    test('skips lines without result.object structure', function (): void {
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        $streamData = implode("\n", [
            '{"result":{"object":"document:budget-2024"}}',
            '{"other":"data"}',
            '{"result":{"something":"else"}}',
            '{"result":{"object":"document:proposal-2024"}}',
            '',
        ]);

        $stream = new SimpleStream($streamData);
        $response = test()->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);

        $generator = StreamedListObjectsResponse::fromResponse($response, $request, $validator);

        $results = [];

        foreach ($generator as $streamedResponse) {
            $results[] = $streamedResponse->getObject();
        }

        // Should only yield lines with result.object structure
        expect($results)->toBe([
            'document:budget-2024',
            'document:proposal-2024',
        ]);
    });

    test('handles network errors (404)', function (): void {
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(404);

        $generator = StreamedListObjectsResponse::fromResponse($response, $request, $validator);

        expect(function () use ($generator): void {
            foreach ($generator as $streamedResponse) {
                // Exception should be thrown during iteration
            }
        })->toThrow(NetworkException::class);
    });

    test('handles server errors (500)', function (): void {
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(500);

        $generator = StreamedListObjectsResponse::fromResponse($response, $request, $validator);

        expect(function () use ($generator): void {
            foreach ($generator as $streamedResponse) {
                // Exception should be thrown during iteration
            }
        })->toThrow(NetworkException::class);
    });

    test('processes single object correctly', function (): void {
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        $streamData = '{"result":{"object":"document:single"}}' . "\n";

        $stream = new SimpleStream($streamData);
        $response = test()->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);

        $generator = StreamedListObjectsResponse::fromResponse($response, $request, $validator);

        $results = [];

        foreach ($generator as $streamedResponse) {
            $results[] = $streamedResponse->getObject();
        }

        expect($results)->toBe(['document:single']);
    });

    test('handles empty stream', function (): void {
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        $stream = new SimpleStream('');
        $response = test()->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);

        $generator = StreamedListObjectsResponse::fromResponse($response, $request, $validator);

        $results = [];

        foreach ($generator as $streamedResponse) {
            $results[] = $streamedResponse->getObject();
        }

        expect($results)->toBe([]);
    });
});
