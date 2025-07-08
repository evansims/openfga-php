<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use OpenFGA\Observability\TelemetryInterface;
use Psr\Http\Client\ClientInterface as PsrHttpClientInterface;
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
    /**
     * Create a new RequestManagerFactory instance.
     *
     * @param string                          $url                  The base URL for API requests
     * @param string|null                     $authorizationHeader  Optional authorization header value
     * @param PsrHttpClientInterface|null     $httpClient          PSR-18 HTTP client implementation
     * @param StreamFactoryInterface|null     $httpStreamFactory   PSR-17 stream factory
     * @param RequestFactoryInterface|null    $httpRequestFactory  PSR-17 request factory
     * @param ResponseFactoryInterface|null   $httpResponseFactory PSR-17 response factory
     * @param TelemetryInterface|null         $telemetry           Telemetry provider for observability
     * @param int                             $defaultMaxRetries   Default number of retry attempts
     * @param HttpClientInterface|null        $httpClientWrapper   Custom HTTP client wrapper
     * @param RetryStrategyInterface|null     $retryStrategy       Custom retry strategy implementation
     * @param ConcurrentExecutorInterface|null $concurrentExecutor  Executor for concurrent operations
     */
    public function __construct(
        private string $url,
        private ?string $authorizationHeader,
        private ?PsrHttpClientInterface $httpClient,
        private ?StreamFactoryInterface $httpStreamFactory,
        private ?RequestFactoryInterface $httpRequestFactory,
        private ?ResponseFactoryInterface $httpResponseFactory,
        private ?TelemetryInterface $telemetry,
        private int $defaultMaxRetries = 3,
        private ?HttpClientInterface $httpClientWrapper = null,
        private ?RetryStrategyInterface $retryStrategy = null,
        private ?ConcurrentExecutorInterface $concurrentExecutor = null,
    ) {
    }

    /**
     * Create a RequestManager for normal operations.
     *
     * @return RequestManager A RequestManager configured with default retry settings
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
            httpClientWrapper: $this->httpClientWrapper,
            retryStrategy: $this->retryStrategy,
            concurrentExecutor: $this->concurrentExecutor,
        );
    }

    /**
     * Create a RequestManager for batch operations (no HTTP retries).
     *
     * @return RequestManager A RequestManager configured with retries disabled
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
            httpClientWrapper: $this->httpClientWrapper,
            retryStrategy: $this->retryStrategy ?? new ExponentialBackoffRetryStrategy(0),
            concurrentExecutor: $this->concurrentExecutor,
        );
    }

    /**
     * Create a RequestManager with custom retry configuration.
     *
     * @param int $maxRetries Maximum number of retry attempts
     *
     * @return RequestManager A RequestManager configured with the specified retry limit
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
            httpClientWrapper: $this->httpClientWrapper,
            retryStrategy: $this->retryStrategy ?? new ExponentialBackoffRetryStrategy($maxRetries),
            concurrentExecutor: $this->concurrentExecutor,
        );
    }
}
