<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use OpenFGA\Observability\TelemetryInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, ResponseFactoryInterface, StreamFactoryInterface};

/**
 * Factory for creating RequestManager instances.
 *
 * This factory encapsulates the creation of RequestManager instances with
 * the appropriate configuration for different use cases (normal requests
 * vs batch operations).
 */
final readonly class RequestManagerFactory
{
    public function __construct(
        private string $url,
        private ?string $authorizationHeader,
        private ?HttpClientInterface $httpClient,
        private ?StreamFactoryInterface $httpStreamFactory,
        private ?RequestFactoryInterface $httpRequestFactory,
        private ?ResponseFactoryInterface $httpResponseFactory,
        private ?TelemetryInterface $telemetry,
        private int $defaultMaxRetries = 3,
    ) {
    }

    /**
     * Create a RequestManager for normal operations.
     */
    public function create(): RequestManager
    {
        return new RequestManager(
            url: $this->url,
            maxRetries: $this->defaultMaxRetries,
            authorizationHeader: $this->authorizationHeader,
            httpClient: $this->httpClient,
            httpStreamFactory: $this->httpStreamFactory,
            httpRequestFactory: $this->httpRequestFactory,
            httpResponseFactory: $this->httpResponseFactory,
            telemetry: $this->telemetry,
        );
    }

    /**
     * Create a RequestManager for batch operations (no HTTP retries).
     */
    public function createForBatch(): RequestManager
    {
        return new RequestManager(
            url: $this->url,
            maxRetries: 0, // Disable HTTP retries for batch operations
            authorizationHeader: $this->authorizationHeader,
            httpClient: $this->httpClient,
            httpStreamFactory: $this->httpStreamFactory,
            httpRequestFactory: $this->httpRequestFactory,
            httpResponseFactory: $this->httpResponseFactory,
            telemetry: $this->telemetry,
        );
    }

    /**
     * Create a RequestManager with custom retry configuration.
     *
     * @param int $maxRetries
     */
    public function createWithRetries(int $maxRetries): RequestManager
    {
        return new RequestManager(
            url: $this->url,
            maxRetries: $maxRetries,
            authorizationHeader: $this->authorizationHeader,
            httpClient: $this->httpClient,
            httpStreamFactory: $this->httpStreamFactory,
            httpRequestFactory: $this->httpRequestFactory,
            httpResponseFactory: $this->httpResponseFactory,
            telemetry: $this->telemetry,
        );
    }
}
