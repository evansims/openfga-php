<?php

declare(strict_types=1);

use Mockery\MockInterface;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\CreateStoreRequest;
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};


it('can be instantiated', function (): void {
    $request = new CreateStoreRequest(name: 'test-store');

    expect($request)->toBeInstanceOf(CreateStoreRequest::class);
    expect($request->getName())->toBe('test-store');
});

it('generates correct request context', function (): void {
    $stream = Mockery::mock(StreamInterface::class);

    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $streamFactory->shouldReceive('createStream')
        ->once()
        ->with(json_encode(['name' => 'my-new-store']))
        ->andReturn($stream);

    $request = new CreateStoreRequest(name: 'my-new-store');
    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::POST);
    expect($context->getUrl())->toBe('/stores/');
    expect($context->getBody())->toBe($stream);
    expect($context->getHeaders())->toBe(['Content-Type' => 'application/json']);
});

it('handles store names with special characters', function (): void {
    $storeName = 'test-store-with-special-chars!@#$%^&*()';
    $stream = Mockery::mock(StreamInterface::class);

    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $streamFactory->shouldReceive('createStream')
        ->once()
        ->with(json_encode(['name' => $storeName]))
        ->andReturn($stream);

    $request = new CreateStoreRequest(name: $storeName);
    $context = $request->getRequest($streamFactory);

    expect($request->getName())->toBe($storeName);
    expect($context->getMethod())->toBe(RequestMethod::POST);
    expect($context->getUrl())->toBe('/stores/');
    expect($context->getBody())->toBe($stream);
});

it('handles empty store name', function (): void {
    $stream = Mockery::mock(StreamInterface::class);

    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $streamFactory->shouldReceive('createStream')
        ->once()
        ->with(json_encode(['name' => '']))
        ->andReturn($stream);

    $request = new CreateStoreRequest(name: '');
    $context = $request->getRequest($streamFactory);

    expect($request->getName())->toBe('');
    expect($context->getMethod())->toBe(RequestMethod::POST);
    expect($context->getUrl())->toBe('/stores/');
});
