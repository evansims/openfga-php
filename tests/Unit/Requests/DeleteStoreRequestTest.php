<?php

declare(strict_types=1);

use InvalidArgumentException;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\DeleteStoreRequest;
use Psr\Http\Message\StreamFactoryInterface;

it('can be instantiated', function (): void {
    $request = new DeleteStoreRequest(store: 'test-store');

    expect($request)->toBeInstanceOf(DeleteStoreRequest::class);
    expect($request->getStore())->toBe('test-store');
});

it('generates correct request context', function (): void {
    $streamFactory = test()->createMock(StreamFactoryInterface::class);

    $request = new DeleteStoreRequest(store: 'store-to-delete');
    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::DELETE);
    expect($context->getUrl())->toBe('/stores/store-to-delete');
    expect($context->getBody())->toBeNull();
    expect($context->getHeaders())->toBe([]);
});

it('handles store IDs with special characters', function (): void {
    $streamFactory = test()->createMock(StreamFactoryInterface::class);

    $storeId = 'store-123-with-special_chars';
    $request = new DeleteStoreRequest(store: $storeId);
    $context = $request->getRequest($streamFactory);

    expect($request->getStore())->toBe($storeId);
    expect($context->getMethod())->toBe(RequestMethod::DELETE);
    expect($context->getUrl())->toBe('/stores/' . $storeId);
});

it('throws when store ID is empty', function (): void {
    $this->expectException(InvalidArgumentException::class);
    new DeleteStoreRequest(store: '');
});
