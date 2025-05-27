<?php

declare(strict_types=1);

use Mockery\MockInterface;
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

    $stream = Mockery::mock(StreamInterface::class);
    $stream->shouldReceive('__toString')
        ->once()
        ->andReturn(json_encode($responseData));

    /** @var MockInterface&ResponseInterface $httpResponse */
    $httpResponse = Mockery::mock(ResponseInterface::class);
    $httpResponse->shouldReceive('getStatusCode')
        ->once()
        ->andReturn(200);
    $httpResponse->shouldReceive('getBody')
        ->once()
        ->andReturn($stream);

    /** @var MockInterface&RequestInterface $request */
    $request = Mockery::mock(RequestInterface::class);

    // Use a real SchemaValidator instance
    $validator = new SchemaValidator();

    $response = CheckResponse::fromResponse($httpResponse, $request, $validator);

    expect($response)->toBeInstanceOf(CheckResponse::class);
    expect($response->getAllowed())->toBe(true);
    expect($response->getResolution())->toBe('conditional');
});

it('handles non-200 status codes', function (): void {
    $stream = Mockery::mock(StreamInterface::class);
    $stream->shouldReceive('__toString')
        ->once()
        ->andReturn('{"error": "Not Found"}');

    /** @var MockInterface&ResponseInterface $httpResponse */
    $httpResponse = Mockery::mock(ResponseInterface::class);
    $httpResponse->shouldReceive('getStatusCode')
        ->atLeast()->once()
        ->andReturn(404);
    $httpResponse->shouldReceive('getBody')
        ->once()
        ->andReturn($stream);

    /** @var MockInterface&RequestInterface $request */
    $request = Mockery::mock(RequestInterface::class);

    $validator = new SchemaValidator();

    expect(fn () => CheckResponse::fromResponse($httpResponse, $request, $validator))
        ->toThrow(Exception::class);
});
