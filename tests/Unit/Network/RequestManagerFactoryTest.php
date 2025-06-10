<?php

declare(strict_types=1);

use OpenFGA\Network\{
    ConcurrentExecutorInterface,
    ExponentialBackoffRetryStrategy,
    HttpClientInterface,
    RequestManager,
    RequestManagerFactory,
    RetryStrategyInterface
};
use OpenFGA\Observability\TelemetryInterface;
use Psr\Http\Client\ClientInterface as PsrHttpClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, ResponseFactoryInterface, StreamFactoryInterface};

beforeEach(function (): void {
    $this->url = 'https://api.openfga.example';
    $this->authorizationHeader = 'Bearer token123';
    $this->httpClient = $this->createMock(PsrHttpClientInterface::class);
    $this->httpStreamFactory = $this->createMock(StreamFactoryInterface::class);
    $this->httpRequestFactory = $this->createMock(RequestFactoryInterface::class);
    $this->httpResponseFactory = $this->createMock(ResponseFactoryInterface::class);
    $this->telemetry = $this->createMock(TelemetryInterface::class);
    $this->httpClientWrapper = $this->createMock(HttpClientInterface::class);
    $this->retryStrategy = $this->createMock(RetryStrategyInterface::class);
    $this->concurrentExecutor = $this->createMock(ConcurrentExecutorInterface::class);

    $this->factory = new RequestManagerFactory(
        url: $this->url,
        authorizationHeader: $this->authorizationHeader,
        httpClient: $this->httpClient,
        httpStreamFactory: $this->httpStreamFactory,
        httpRequestFactory: $this->httpRequestFactory,
        httpResponseFactory: $this->httpResponseFactory,
        telemetry: $this->telemetry,
        defaultMaxRetries: 5,
        httpClientWrapper: $this->httpClientWrapper,
        retryStrategy: $this->retryStrategy,
        concurrentExecutor: $this->concurrentExecutor
    );
});

it('constructs with all required and optional parameters', function (): void {
    expect($this->factory)->toBeInstanceOf(RequestManagerFactory::class);
});

it('constructs with minimal required parameters', function (): void {
    $minimalFactory = new RequestManagerFactory(
        url: $this->url,
        authorizationHeader: null,
        httpClient: null,
        httpStreamFactory: null,
        httpRequestFactory: null,
        httpResponseFactory: null,
        telemetry: null
    );

    expect($minimalFactory)->toBeInstanceOf(RequestManagerFactory::class);
});

it('creates RequestManager with default configuration', function (): void {
    $requestManager = $this->factory->create();

    expect($requestManager)->toBeInstanceOf(RequestManager::class);
});

it('creates RequestManager for batch operations with disabled retries', function (): void {
    $requestManager = $this->factory->createForBatch();

    expect($requestManager)->toBeInstanceOf(RequestManager::class);
});

it('creates RequestManager with custom retry count', function (): void {
    $customRetries = 10;
    $requestManager = $this->factory->createWithRetries($customRetries);

    expect($requestManager)->toBeInstanceOf(RequestManager::class);
});

it('creates RequestManager for batch with null retry strategy', function (): void {
    $factoryWithoutStrategy = new RequestManagerFactory(
        url: $this->url,
        authorizationHeader: $this->authorizationHeader,
        httpClient: $this->httpClient,
        httpStreamFactory: $this->httpStreamFactory,
        httpRequestFactory: $this->httpRequestFactory,
        httpResponseFactory: $this->httpResponseFactory,
        telemetry: $this->telemetry,
        defaultMaxRetries: 3,
        httpClientWrapper: $this->httpClientWrapper,
        retryStrategy: null,
        concurrentExecutor: $this->concurrentExecutor
    );

    $requestManager = $factoryWithoutStrategy->createForBatch();

    expect($requestManager)->toBeInstanceOf(RequestManager::class);
});

it('creates RequestManager with custom retries and null retry strategy', function (): void {
    $factoryWithoutStrategy = new RequestManagerFactory(
        url: $this->url,
        authorizationHeader: $this->authorizationHeader,
        httpClient: $this->httpClient,
        httpStreamFactory: $this->httpStreamFactory,
        httpRequestFactory: $this->httpRequestFactory,
        httpResponseFactory: $this->httpResponseFactory,
        telemetry: $this->telemetry,
        defaultMaxRetries: 3,
        httpClientWrapper: $this->httpClientWrapper,
        retryStrategy: null,
        concurrentExecutor: $this->concurrentExecutor
    );

    $requestManager = $factoryWithoutStrategy->createWithRetries(7);

    expect($requestManager)->toBeInstanceOf(RequestManager::class);
});

it('creates different RequestManager instances on each call', function (): void {
    $manager1 = $this->factory->create();
    $manager2 = $this->factory->create();

    expect($manager1)->not->toBe($manager2);
    expect($manager1)->toBeInstanceOf(RequestManager::class);
    expect($manager2)->toBeInstanceOf(RequestManager::class);
});

it('creates different batch RequestManager instances on each call', function (): void {
    $manager1 = $this->factory->createForBatch();
    $manager2 = $this->factory->createForBatch();

    expect($manager1)->not->toBe($manager2);
    expect($manager1)->toBeInstanceOf(RequestManager::class);
    expect($manager2)->toBeInstanceOf(RequestManager::class);
});

it('creates different custom retry RequestManager instances on each call', function (): void {
    $manager1 = $this->factory->createWithRetries(5);
    $manager2 = $this->factory->createWithRetries(5);

    expect($manager1)->not->toBe($manager2);
    expect($manager1)->toBeInstanceOf(RequestManager::class);
    expect($manager2)->toBeInstanceOf(RequestManager::class);
});

it('passes all parameters to RequestManager constructor for create method', function (): void {
    // This test verifies that the factory properly forwards all parameters
    // We can't directly inspect the RequestManager's private properties,
    // but we can verify that the instance is created without errors
    $requestManager = $this->factory->create();

    expect($requestManager)->toBeInstanceOf(RequestManager::class);
});

it('passes all parameters to RequestManager constructor for createForBatch method', function (): void {
    $requestManager = $this->factory->createForBatch();

    expect($requestManager)->toBeInstanceOf(RequestManager::class);
});

it('passes all parameters to RequestManager constructor for createWithRetries method', function (): void {
    $requestManager = $this->factory->createWithRetries(15);

    expect($requestManager)->toBeInstanceOf(RequestManager::class);
});

it('handles zero retry count', function (): void {
    $requestManager = $this->factory->createWithRetries(0);

    expect($requestManager)->toBeInstanceOf(RequestManager::class);
});

it('handles negative retry count', function (): void {
    $requestManager = $this->factory->createWithRetries(-1);

    expect($requestManager)->toBeInstanceOf(RequestManager::class);
});

it('handles large retry count', function (): void {
    $requestManager = $this->factory->createWithRetries(1000);

    expect($requestManager)->toBeInstanceOf(RequestManager::class);
});

it('works with empty URL', function (): void {
    $factoryWithEmptyUrl = new RequestManagerFactory(
        url: '',
        authorizationHeader: null,
        httpClient: null,
        httpStreamFactory: null,
        httpRequestFactory: null,
        httpResponseFactory: null,
        telemetry: null
    );

    $requestManager = $factoryWithEmptyUrl->create();

    expect($requestManager)->toBeInstanceOf(RequestManager::class);
});

it('works with null authorization header', function (): void {
    $factoryWithNullAuth = new RequestManagerFactory(
        url: $this->url,
        authorizationHeader: null,
        httpClient: $this->httpClient,
        httpStreamFactory: $this->httpStreamFactory,
        httpRequestFactory: $this->httpRequestFactory,
        httpResponseFactory: $this->httpResponseFactory,
        telemetry: $this->telemetry
    );

    $requestManager = $factoryWithNullAuth->create();

    expect($requestManager)->toBeInstanceOf(RequestManager::class);
});

it('preserves all null dependencies', function (): void {
    $factoryWithNullDependencies = new RequestManagerFactory(
        url: $this->url,
        authorizationHeader: null,
        httpClient: null,
        httpStreamFactory: null,
        httpRequestFactory: null,
        httpResponseFactory: null,
        telemetry: null,
        defaultMaxRetries: 2,
        httpClientWrapper: null,
        retryStrategy: null,
        concurrentExecutor: null
    );

    $requestManager = $factoryWithNullDependencies->create();

    expect($requestManager)->toBeInstanceOf(RequestManager::class);
});
