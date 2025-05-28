<?php

declare(strict_types=1);

use OpenFGA\Responses\{DeleteStoreResponse, DeleteStoreResponseInterface};
use OpenFGA\Schema\SchemaValidator;
use Psr\Http\Message\{RequestInterface, ResponseInterface, StreamInterface};

beforeEach(function (): void {
    $this->validator = new SchemaValidator();
    $this->request = test()->createMock(RequestInterface::class);
});

test('DeleteStoreResponse implements DeleteStoreResponseInterface', function (): void {
    $response = new DeleteStoreResponse();

    expect($response)->toBeInstanceOf(DeleteStoreResponseInterface::class);
});

test('DeleteStoreResponse can be instantiated without parameters', function (): void {
    $response = new DeleteStoreResponse();

    expect($response)->toBeInstanceOf(DeleteStoreResponse::class);
});

test('DeleteStoreResponse is a simple response class', function (): void {
    $response = new DeleteStoreResponse();

    // DeleteStoreResponse is a simple response with no data
    // It represents a successful delete operation (204 No Content)
    expect($response)->toBeInstanceOf(DeleteStoreResponseInterface::class);
});

test('DeleteStoreResponse fromResponse handles successful 204 response', function (): void {
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('__toString')
        ->willReturn(''); // 204 responses typically have empty body

    $httpResponse = test()->createMock(ResponseInterface::class);
    $httpResponse->method('getStatusCode')
        ->willReturn(204);
    $httpResponse->method('getBody')
        ->willReturn($stream);

    $response = DeleteStoreResponse::fromResponse($httpResponse, $this->request, $this->validator);

    expect($response)->toBeInstanceOf(DeleteStoreResponseInterface::class);
    expect($response)->toBeInstanceOf(DeleteStoreResponse::class);
});

test('DeleteStoreResponse fromResponse handles 204 with empty body', function (): void {
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('__toString')
        ->willReturn('');

    $httpResponse = test()->createMock(ResponseInterface::class);
    $httpResponse->method('getStatusCode')
        ->willReturn(204);
    $httpResponse->method('getBody')
        ->willReturn($stream);

    $response = DeleteStoreResponse::fromResponse($httpResponse, $this->request, $this->validator);

    expect($response)->toBeInstanceOf(DeleteStoreResponse::class);
});

test('DeleteStoreResponse fromResponse handles 204 with whitespace body', function (): void {
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('__toString')
        ->willReturn('   '); // Some servers might return whitespace

    $httpResponse = test()->createMock(ResponseInterface::class);
    $httpResponse->method('getStatusCode')
        ->willReturn(204);
    $httpResponse->method('getBody')
        ->willReturn($stream);

    $response = DeleteStoreResponse::fromResponse($httpResponse, $this->request, $this->validator);

    expect($response)->toBeInstanceOf(DeleteStoreResponse::class);
});

test('DeleteStoreResponse fromResponse handles non-204 status codes', function (): void {
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('__toString')
        ->willReturn('{"error": "Bad Request"}');

    $httpResponse = test()->createMock(ResponseInterface::class);
    $httpResponse->method('getStatusCode')
        ->willReturn(400);
    $httpResponse->method('getBody')
        ->willReturn($stream);

    expect(fn () => DeleteStoreResponse::fromResponse($httpResponse, $this->request, $this->validator))
        ->toThrow(Exception::class);
});

test('DeleteStoreResponse fromResponse handles 403 Forbidden', function (): void {
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('__toString')
        ->willReturn('{"error": "Forbidden"}');

    $httpResponse = test()->createMock(ResponseInterface::class);
    $httpResponse->method('getStatusCode')
        ->willReturn(403);
    $httpResponse->method('getBody')
        ->willReturn($stream);

    expect(fn () => DeleteStoreResponse::fromResponse($httpResponse, $this->request, $this->validator))
        ->toThrow(Exception::class);
});

test('DeleteStoreResponse fromResponse handles 404 Not Found', function (): void {
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('__toString')
        ->willReturn('{"error": "Store not found"}');

    $httpResponse = test()->createMock(ResponseInterface::class);
    $httpResponse->method('getStatusCode')
        ->willReturn(404);
    $httpResponse->method('getBody')
        ->willReturn($stream);

    expect(fn () => DeleteStoreResponse::fromResponse($httpResponse, $this->request, $this->validator))
        ->toThrow(Exception::class);
});

test('DeleteStoreResponse fromResponse handles 500 Internal Server Error', function (): void {
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('__toString')
        ->willReturn('{"error": "Internal Server Error"}');

    $httpResponse = test()->createMock(ResponseInterface::class);
    $httpResponse->method('getStatusCode')
        ->willReturn(500);
    $httpResponse->method('getBody')
        ->willReturn($stream);

    expect(fn () => DeleteStoreResponse::fromResponse($httpResponse, $this->request, $this->validator))
        ->toThrow(Exception::class);
});

test('DeleteStoreResponse fromResponse handles 422 Unprocessable Entity', function (): void {
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('__toString')
        ->willReturn('{"error": "Validation failed"}');

    $httpResponse = test()->createMock(ResponseInterface::class);
    $httpResponse->method('getStatusCode')
        ->willReturn(422);
    $httpResponse->method('getBody')
        ->willReturn($stream);

    expect(fn () => DeleteStoreResponse::fromResponse($httpResponse, $this->request, $this->validator))
        ->toThrow(Exception::class);
});

test('DeleteStoreResponse fromResponse handles 200 status code as non-success', function (): void {
    // DeleteStore should return 204, not 200
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('__toString')
        ->willReturn('{"unexpected": "success response"}');

    $httpResponse = test()->createMock(ResponseInterface::class);
    $httpResponse->method('getStatusCode')
        ->willReturn(200);
    $httpResponse->method('getBody')
        ->willReturn($stream);

    expect(fn () => DeleteStoreResponse::fromResponse($httpResponse, $this->request, $this->validator))
        ->toThrow(Exception::class);
});

test('DeleteStoreResponse fromResponse handles 409 Conflict', function (): void {
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('__toString')
        ->willReturn('{"error": "Store has active resources"}');

    $httpResponse = test()->createMock(ResponseInterface::class);
    $httpResponse->method('getStatusCode')
        ->willReturn(409);
    $httpResponse->method('getBody')
        ->willReturn($stream);

    expect(fn () => DeleteStoreResponse::fromResponse($httpResponse, $this->request, $this->validator))
        ->toThrow(Exception::class);
});

test('DeleteStoreResponse multiple instances are independent', function (): void {
    $response1 = new DeleteStoreResponse();
    $response2 = new DeleteStoreResponse();

    expect($response1)->not->toBe($response2);
    expect($response1)->toBeInstanceOf(DeleteStoreResponse::class);
    expect($response2)->toBeInstanceOf(DeleteStoreResponse::class);
});

test('DeleteStoreResponse fromResponse maintains consistency', function (): void {
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('__toString')
        ->willReturn('');

    $httpResponse = test()->createMock(ResponseInterface::class);
    $httpResponse->method('getStatusCode')
        ->willReturn(204);
    $httpResponse->method('getBody')
        ->willReturn($stream);

    $response1 = DeleteStoreResponse::fromResponse($httpResponse, $this->request, $this->validator);
    $response2 = DeleteStoreResponse::fromResponse($httpResponse, $this->request, $this->validator);

    expect($response1)->not->toBe($response2);
    expect($response1)->toBeInstanceOf(DeleteStoreResponse::class);
    expect($response2)->toBeInstanceOf(DeleteStoreResponse::class);
});
