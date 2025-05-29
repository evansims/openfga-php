<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Responses;

use Exception;
use OpenFGA\Responses\CheckResponse;
use OpenFGA\Schema\{SchemaInterface, SchemaValidator};
use Psr\Http\Message\{RequestInterface, ResponseInterface, StreamInterface};

it('can be instantiated with all parameters', function (): void {
    $response = new CheckResponse(
        allowed: true,
        resolution: 'direct',
    );

    expect($response)->toBeInstanceOf(CheckResponse::class);
    expect($response->getAllowed())->toBe(true);
    expect($response->getResolution())->toBe('direct');
});

it('can be instantiated with null values', function (): void {
    $response = new CheckResponse();

    expect($response)->toBeInstanceOf(CheckResponse::class);
    expect($response->getAllowed())->toBeNull();
    expect($response->getResolution())->toBeNull();
});

it('can be instantiated with partial parameters', function (): void {
    $response = new CheckResponse(allowed: false);

    expect($response->getAllowed())->toBe(false);
    expect($response->getResolution())->toBeNull();
});

it('returns correct schema', function (): void {
    $schema = CheckResponse::schema();

    expect($schema)->toBeInstanceOf(SchemaInterface::class);
    expect($schema->getClassName())->toBe(CheckResponse::class);

    $properties = $schema->getProperties();
    expect($properties)->toBeArray();
    expect($properties)->toHaveKey('allowed');
    expect($properties)->toHaveKey('resolution');

    expect($properties['allowed']->name)->toBe('allowed');
    expect($properties['allowed']->type)->toBe('boolean');
    expect($properties['allowed']->required)->toBe(false);

    expect($properties['resolution']->name)->toBe('resolution');
    expect($properties['resolution']->type)->toBe('string');
    expect($properties['resolution']->required)->toBe(false);
});

it('schema is cached', function (): void {
    $schema1 = CheckResponse::schema();
    $schema2 = CheckResponse::schema();

    expect($schema1)->toBe($schema2);
});

it('creates response from successful HTTP response', function (): void {
    $responseData = ['allowed' => true, 'resolution' => 'conditional'];

    $stream = test()->createMock(StreamInterface::class);
    $stream->method('__toString')
        ->willReturn(json_encode($responseData));

    $httpResponse = test()->createMock(ResponseInterface::class);
    $httpResponse->method('getStatusCode')
        ->willReturn(200);
    $httpResponse->method('getBody')
        ->willReturn($stream);

    $request = test()->createMock(RequestInterface::class);

    // Use a real SchemaValidator instance
    $validator = new SchemaValidator();

    $response = CheckResponse::fromResponse($httpResponse, $request, $validator);

    expect($response)->toBeInstanceOf(CheckResponse::class);
    expect($response->getAllowed())->toBe(true);
    expect($response->getResolution())->toBe('conditional');
});

it('handles non-200 status codes', function (): void {
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('__toString')
        ->willReturn('{"error": "Not Found"}');

    $httpResponse = test()->createMock(ResponseInterface::class);
    $httpResponse->method('getStatusCode')
        ->willReturn(404);
    $httpResponse->method('getBody')
        ->willReturn($stream);

    $request = test()->createMock(RequestInterface::class);

    $validator = new SchemaValidator();

    $this->expectException(Exception::class);
    CheckResponse::fromResponse($httpResponse, $request, $validator);
});
