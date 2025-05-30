<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Responses;

use Exception;
use OpenFGA\Responses\{WriteTuplesResponse, WriteTuplesResponseInterface};
use OpenFGA\Schema\SchemaValidator;
use Psr\Http\Message\{RequestInterface, ResponseInterface, StreamInterface};

describe('WriteTuplesResponse', function (): void {
    beforeEach(function (): void {
        $this->validator = new SchemaValidator;
        $this->request = test()->createMock(RequestInterface::class);
    });

    test('implements WriteTuplesResponseInterface', function (): void {
        $response = new WriteTuplesResponse;

        expect($response)->toBeInstanceOf(WriteTuplesResponseInterface::class);
    });

    test('can be instantiated without parameters', function (): void {
        $response = new WriteTuplesResponse;

        expect($response)->toBeInstanceOf(WriteTuplesResponse::class);
    });

    test('is a simple response class', function (): void {
        $response = new WriteTuplesResponse;

        // WriteTuplesResponse is a simple response with no data
        // It represents a successful write operation (200 OK)
        expect($response)->toBeInstanceOf(WriteTuplesResponseInterface::class);
    });

    test('fromResponse handles successful 200 response', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{}'); // 200 responses may have empty JSON body

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(200);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $response = WriteTuplesResponse::fromResponse($httpResponse, $this->request, $this->validator);

        expect($response)->toBeInstanceOf(WriteTuplesResponseInterface::class);
        expect($response)->toBeInstanceOf(WriteTuplesResponse::class);
    });

    test('fromResponse handles 200 with empty body', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(200);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $response = WriteTuplesResponse::fromResponse($httpResponse, $this->request, $this->validator);

        expect($response)->toBeInstanceOf(WriteTuplesResponse::class);
    });

    test('fromResponse handles 200 with whitespace body', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('   '); // Some servers might return whitespace

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(200);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $response = WriteTuplesResponse::fromResponse($httpResponse, $this->request, $this->validator);

        expect($response)->toBeInstanceOf(WriteTuplesResponse::class);
    });

    test('fromResponse handles non-200 status codes', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"error": "Bad Request"}');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(400);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        WriteTuplesResponse::fromResponse($httpResponse, $this->request, $this->validator);
    })->throws(Exception::class);

    test('fromResponse handles 403 Forbidden', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"error": "Forbidden"}');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(403);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        WriteTuplesResponse::fromResponse($httpResponse, $this->request, $this->validator);
    })->throws(Exception::class);

    test('fromResponse handles 404 Not Found', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"error": "Not Found"}');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(404);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        WriteTuplesResponse::fromResponse($httpResponse, $this->request, $this->validator);
    })->throws(Exception::class);

    test('fromResponse handles 500 Internal Server Error', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"error": "Internal Server Error"}');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(500);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        WriteTuplesResponse::fromResponse($httpResponse, $this->request, $this->validator);
    })->throws(Exception::class);

    test('fromResponse handles 422 Unprocessable Entity', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"error": "Validation failed"}');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(422);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        WriteTuplesResponse::fromResponse($httpResponse, $this->request, $this->validator);
    })->throws(Exception::class);

    test('fromResponse handles 204 status code as success', function (): void {
        // WriteTuples can also return 204 No Content
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(204);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $response = WriteTuplesResponse::fromResponse($httpResponse, $this->request, $this->validator);

        expect($response)->toBeInstanceOf(WriteTuplesResponseInterface::class);
        expect($response)->toBeInstanceOf(WriteTuplesResponse::class);
    });

    test('fromResponse handles 201 status code as non-success', function (): void {
        // WriteTuples should return 200, not 201
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"created": "resource"}');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(201);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        WriteTuplesResponse::fromResponse($httpResponse, $this->request, $this->validator);
    })->throws(Exception::class);

    test('multiple instances are independent', function (): void {
        $response1 = new WriteTuplesResponse;
        $response2 = new WriteTuplesResponse;

        expect($response1)->not->toBe($response2);
        expect($response1)->toBeInstanceOf(WriteTuplesResponse::class);
        expect($response2)->toBeInstanceOf(WriteTuplesResponse::class);
    });

    test('fromResponse maintains consistency', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{}');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(200);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $response1 = WriteTuplesResponse::fromResponse($httpResponse, $this->request, $this->validator);
        $response2 = WriteTuplesResponse::fromResponse($httpResponse, $this->request, $this->validator);

        expect($response1)->not->toBe($response2);
        expect($response1)->toBeInstanceOf(WriteTuplesResponse::class);
        expect($response2)->toBeInstanceOf(WriteTuplesResponse::class);
    });
});
