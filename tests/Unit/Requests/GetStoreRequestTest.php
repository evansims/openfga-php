<?php

declare(strict_types=1);

use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\GetStoreRequest;
use Psr\Http\Message\StreamFactoryInterface;

it('can be instantiated', function (): void {
    $request = new GetStoreRequest(store: 'test-store');

    expect($request)->toBeInstanceOf(GetStoreRequest::class);
    expect($request->getStore())->toBe('test-store');
});

it('generates correct request context', function (): void {
    $streamFactory = test()->createMock(StreamFactoryInterface::class);

    $request = new GetStoreRequest(store: 'my-store');
    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::GET);
    expect($context->getUrl())->toBe('/stores/my-store');
    expect($context->getBody())->toBeNull();
    expect($context->getHeaders())->toBe([]);
});

it('handles store IDs with special characters', function (): void {
    $streamFactory = test()->createMock(StreamFactoryInterface::class);

    $storeId = 'store-123-with-special_chars';
    $request = new GetStoreRequest(store: $storeId);
    $context = $request->getRequest($streamFactory);

    expect($request->getStore())->toBe($storeId);
    expect($context->getMethod())->toBe(RequestMethod::GET);
    expect($context->getUrl())->toBe('/stores/' . $storeId);
});

it('throws when store ID is empty', function (): void {
    expect(fn () => new GetStoreRequest(store: ''))
        ->toThrow(InvalidArgumentException::class);
});
