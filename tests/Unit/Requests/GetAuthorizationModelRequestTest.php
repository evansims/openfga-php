<?php

declare(strict_types=1);

use Mockery\MockInterface;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\GetAuthorizationModelRequest;
use Psr\Http\Message\StreamFactoryInterface;


it('can be instantiated', function (): void {
    $request = new GetAuthorizationModelRequest(
        store: 'test-store',
        model: 'test-model',
    );

    expect($request)->toBeInstanceOf(GetAuthorizationModelRequest::class);
    expect($request->getStore())->toBe('test-store');
    expect($request->getModel())->toBe('test-model');
});

it('generates correct request context', function (): void {
    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $request = new GetAuthorizationModelRequest(
        store: 'my-store',
        model: 'model-123',
    );
    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::GET);
    expect($context->getUrl())->toBe('/stores/my-store/authorization-models/model-123');
    expect($context->getBody())->toBeNull();
    expect($context->getHeaders())->toBe([]);
});

it('handles special characters in store and model IDs', function (): void {
    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $storeId = 'store-with-special_chars';
    $modelId = 'model_with-123-special';

    $request = new GetAuthorizationModelRequest(
        store: $storeId,
        model: $modelId,
    );
    $context = $request->getRequest($streamFactory);

    expect($request->getStore())->toBe($storeId);
    expect($request->getModel())->toBe($modelId);
    expect($context->getMethod())->toBe(RequestMethod::GET);
    expect($context->getUrl())->toBe('/stores/' . $storeId . '/authorization-models/' . $modelId);
});

it('handles empty store and model IDs', function (): void {
    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    $request = new GetAuthorizationModelRequest(store: '', model: '');
    $context = $request->getRequest($streamFactory);

    expect($request->getStore())->toBe('');
    expect($request->getModel())->toBe('');
    expect($context->getMethod())->toBe(RequestMethod::GET);
    expect($context->getUrl())->toBe('/stores//authorization-models/');
});
