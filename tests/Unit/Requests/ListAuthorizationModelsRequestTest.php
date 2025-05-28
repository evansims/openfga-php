<?php

declare(strict_types=1);

use Mockery\MockInterface;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\ListAuthorizationModelsRequest;
use Psr\Http\Message\StreamFactoryInterface;


it('can be instantiated with required parameters', function (): void {
    $request = new ListAuthorizationModelsRequest(store: 'test-store');

    expect($request)->toBeInstanceOf(ListAuthorizationModelsRequest::class);
    expect($request->getStore())->toBe('test-store');
    expect($request->getContinuationToken())->toBeNull();
    expect($request->getPageSize())->toBeNull();
});

it('can be instantiated with all parameters', function (): void {
    $request = new ListAuthorizationModelsRequest(
        store: 'test-store',
        continuationToken: 'next-page-token',
        pageSize: 50,
    );

    expect($request->getStore())->toBe('test-store');
    expect($request->getContinuationToken())->toBe('next-page-token');
    expect($request->getPageSize())->toBe(50);
});

it('generates correct request context without pagination', function (): void {
    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $request = new ListAuthorizationModelsRequest(store: 'my-store');
    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::GET);
    expect($context->getUrl())->toBe('/stores/my-store/authorization-models');
    expect($context->getBody())->toBeNull();
    expect($context->getHeaders())->toBe([]);
});

it('generates correct request context with continuation token', function (): void {
    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $request = new ListAuthorizationModelsRequest(
        store: 'my-store',
        continuationToken: 'token123',
    );
    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::GET);
    expect($context->getUrl())->toBe('/stores/my-store/authorization-models?continuation_token=token123');
});

it('generates correct request context with page size', function (): void {
    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $request = new ListAuthorizationModelsRequest(
        store: 'my-store',
        pageSize: 25,
    );
    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::GET);
    expect($context->getUrl())->toBe('/stores/my-store/authorization-models?page_size=25');
});

it('generates correct request context with all pagination parameters', function (): void {
    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $request = new ListAuthorizationModelsRequest(
        store: 'my-store',
        continuationToken: 'next-token',
        pageSize: 100,
    );
    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::GET);
    expect($context->getUrl())->toBe('/stores/my-store/authorization-models?continuation_token=next-token&page_size=100');
});

it('handles special characters in continuation token', function (): void {
    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $token = 'token with spaces & special=chars';
    $request = new ListAuthorizationModelsRequest(
        store: 'my-store',
        continuationToken: $token,
    );
    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::GET);
    expect($context->getUrl())->toBe('/stores/my-store/authorization-models?continuation_token=' . urlencode($token));
});

it('filters out null values from query parameters', function (): void {
    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $request = new ListAuthorizationModelsRequest(
        store: 'my-store',
        continuationToken: null,
        pageSize: null,
    );
    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::GET);
    expect($context->getUrl())->toBe('/stores/my-store/authorization-models');
});
