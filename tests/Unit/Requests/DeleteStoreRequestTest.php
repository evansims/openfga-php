<?php

declare(strict_types=1);

use Mockery\MockInterface;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\DeleteStoreRequest;
use Psr\Http\Message\StreamFactoryInterface;


it('can be instantiated', function (): void {
    $request = new DeleteStoreRequest(store: 'test-store');

    expect($request)->toBeInstanceOf(DeleteStoreRequest::class);
    expect($request->getStore())->toBe('test-store');
});

it('generates correct request context', function (): void {
    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $request = new DeleteStoreRequest(store: 'store-to-delete');
    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::DELETE);
    expect($context->getUrl())->toBe('/stores/store-to-delete');
    expect($context->getBody())->toBeNull();
    expect($context->getHeaders())->toBe([]);
});

it('handles store IDs with special characters', function (): void {
    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $storeId = 'store-123-with-special_chars';
    $request = new DeleteStoreRequest(store: $storeId);
    $context = $request->getRequest($streamFactory);

    expect($request->getStore())->toBe($storeId);
    expect($context->getMethod())->toBe(RequestMethod::DELETE);
    expect($context->getUrl())->toBe('/stores/' . $storeId);
});

it('handles empty store ID', function (): void {
    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $request = new DeleteStoreRequest(store: '');
    $context = $request->getRequest($streamFactory);

    expect($request->getStore())->toBe('');
    expect($context->getMethod())->toBe(RequestMethod::DELETE);
    expect($context->getUrl())->toBe('/stores/');
});
