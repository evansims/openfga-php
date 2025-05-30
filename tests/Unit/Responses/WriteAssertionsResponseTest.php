<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Responses;

use Exception;
use OpenFGA\Responses\{WriteAssertionsResponse, WriteAssertionsResponseInterface};
use OpenFGA\Schema\SchemaValidator;
use Psr\Http\Message\{RequestInterface, ResponseInterface, StreamInterface};

describe('WriteAssertionsResponse', function (): void {
    beforeEach(function (): void {
        $this->validator = new SchemaValidator();
        $this->request = test()->createMock(RequestInterface::class);
    });

    test('implements WriteAssertionsResponseInterface', function (): void {
        $response = new WriteAssertionsResponse();

        expect($response)->toBeInstanceOf(WriteAssertionsResponseInterface::class);
    });

    test('can be instantiated without parameters', function (): void {
        $response = new WriteAssertionsResponse();

        expect($response)->toBeInstanceOf(WriteAssertionsResponse::class);
    });

    test('is a simple response class', function (): void {
        $response = new WriteAssertionsResponse();

        expect($response)->toBeInstanceOf(WriteAssertionsResponseInterface::class);
    });

    test('fromResponse handles successful 204 response', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(204);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $response = WriteAssertionsResponse::fromResponse($httpResponse, $this->request, $this->validator);

        expect($response)->toBeInstanceOf(WriteAssertionsResponseInterface::class);
        expect($response)->toBeInstanceOf(WriteAssertionsResponse::class);
    });

    test('fromResponse handles 204 with empty body', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(204);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $response = WriteAssertionsResponse::fromResponse($httpResponse, $this->request, $this->validator);

        expect($response)->toBeInstanceOf(WriteAssertionsResponse::class);
    });

    test('fromResponse handles 204 with whitespace body', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('   ');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(204);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $response = WriteAssertionsResponse::fromResponse($httpResponse, $this->request, $this->validator);

        expect($response)->toBeInstanceOf(WriteAssertionsResponse::class);
    });

    test('fromResponse handles non-204 status codes', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"error": "Bad Request"}');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(400);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $this->expectException(Exception::class);
        WriteAssertionsResponse::fromResponse($httpResponse, $this->request, $this->validator);
    });

    test('fromResponse handles 403 Forbidden', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"error": "Forbidden"}');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(403);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $this->expectException(Exception::class);
        WriteAssertionsResponse::fromResponse($httpResponse, $this->request, $this->validator);
    });

    test('fromResponse handles 404 Not Found', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"error": "Not Found"}');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(404);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $this->expectException(Exception::class);
        WriteAssertionsResponse::fromResponse($httpResponse, $this->request, $this->validator);
    });

    test('fromResponse handles 500 Internal Server Error', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"error": "Internal Server Error"}');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(500);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $this->expectException(Exception::class);
        WriteAssertionsResponse::fromResponse($httpResponse, $this->request, $this->validator);
    });

    test('fromResponse handles 422 Unprocessable Entity', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"error": "Validation failed"}');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(422);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $this->expectException(Exception::class);
        WriteAssertionsResponse::fromResponse($httpResponse, $this->request, $this->validator);
    });

    test('fromResponse handles 200 status code as non-success', function (): void {
        // WriteAssertions should return 204, not 200
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"unexpected": "success response"}');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(200);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $this->expectException(Exception::class);
        WriteAssertionsResponse::fromResponse($httpResponse, $this->request, $this->validator);
    });

    test('fromResponse handles 201 status code as non-success', function (): void {
        // WriteAssertions should return 204, not 201
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('{"created": "resource"}');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(201);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $this->expectException(Exception::class);
        WriteAssertionsResponse::fromResponse($httpResponse, $this->request, $this->validator);
    });

    test('multiple instances are independent', function (): void {
        $response1 = new WriteAssertionsResponse();
        $response2 = new WriteAssertionsResponse();

        expect($response1)->not->toBe($response2);
        expect($response1)->toBeInstanceOf(WriteAssertionsResponse::class);
        expect($response2)->toBeInstanceOf(WriteAssertionsResponse::class);
    });

    test('fromResponse maintains consistency', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn('');

        $httpResponse = test()->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')
            ->willReturn(204);
        $httpResponse->method('getBody')
            ->willReturn($stream);

        $response1 = WriteAssertionsResponse::fromResponse($httpResponse, $this->request, $this->validator);
        $response2 = WriteAssertionsResponse::fromResponse($httpResponse, $this->request, $this->validator);

        expect($response1)->not->toBe($response2);
        expect($response1)->toBeInstanceOf(WriteAssertionsResponse::class);
        expect($response2)->toBeInstanceOf(WriteAssertionsResponse::class);
    });
});
