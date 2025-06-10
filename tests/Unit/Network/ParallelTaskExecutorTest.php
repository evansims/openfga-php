<?php

declare(strict_types=1);

use OpenFGA\Network\{ParallelTaskExecutor, RequestManagerFactory};
use OpenFGA\Observability\TelemetryInterface;
use Psr\Http\Client\ClientInterface as PsrHttpClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, ResponseFactoryInterface, StreamFactoryInterface};

beforeEach(function (): void {
    // Create a real factory since it's readonly/final and we can't mock it
    $this->factory = new RequestManagerFactory(
        url: 'https://api.example.com',
        authorizationHeader: null,
        httpClient: $this->createMock(PsrHttpClientInterface::class),
        httpStreamFactory: $this->createMock(StreamFactoryInterface::class),
        httpRequestFactory: $this->createMock(RequestFactoryInterface::class),
        httpResponseFactory: $this->createMock(ResponseFactoryInterface::class),
        telemetry: $this->createMock(TelemetryInterface::class),
    );

    $this->executor = new ParallelTaskExecutor($this->factory);
});

it('constructs with request manager factory', function (): void {
    expect($this->executor)->toBeInstanceOf(ParallelTaskExecutor::class);
});

it('can be constructed with factory', function (): void {
    $factory = new RequestManagerFactory(
        url: 'https://test.example.com',
        authorizationHeader: 'Bearer test',
        httpClient: null,
        httpStreamFactory: null,
        httpRequestFactory: null,
        httpResponseFactory: null,
        telemetry: null,
    );

    $executor = new ParallelTaskExecutor($factory);

    expect($executor)->toBeInstanceOf(ParallelTaskExecutor::class);
});
