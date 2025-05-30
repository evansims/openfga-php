<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use const JSON_THROW_ON_ERROR;

use Exception;
use InvalidArgumentException;
use OpenFGA\{Client, Messages};
use OpenFGA\Exceptions\{ConfigurationError, NetworkError, NetworkException};
use OpenFGA\Observability\TelemetryInterface;
use OpenFGA\Requests\RequestInterface as ClientRequestInterface;
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface, ResponseFactoryInterface, ResponseInterface, StreamFactoryInterface, StreamInterface};
use PsrDiscovery\Discover;
use ReflectionException;
use Throwable;

use function is_string;
use function sprintf;

/**
 * Concrete implementation of HTTP request management for OpenFGA API communication.
 *
 * This class provides the core HTTP communication layer for the OpenFGA SDK,
 * handling all aspects of request construction, execution, and response processing.
 * It integrates with PSR-7 HTTP message interfaces and PSR-18 HTTP clients to
 * provide a flexible, testable HTTP transport layer.
 *
 * The RequestManager manages:
 * - PSR-17 factory auto-discovery and configuration
 * - HTTP client configuration and request execution
 * - Authentication header management
 * - Request URL construction and routing
 * - Error response parsing and exception handling
 * - User-Agent header management for SDK identification
 *
 * The implementation uses lazy initialization for PSR components, automatically
 * discovering suitable implementations when not explicitly provided. This ensures
 * compatibility with a wide range of HTTP libraries while maintaining optimal
 * performance.
 *
 * @see RequestManagerInterface Request manager interface
 * @see ClientInterface PSR-18 HTTP client
 */
final class RequestManager implements RequestManagerInterface
{
    /**
     * Circuit breaker for preventing cascade failures.
     */
    private readonly CircuitBreakerInterface $circuitBreaker;

    /**
     * Retry handler for implementing retry logic.
     */
    private readonly RetryHandlerInterface $retryHandler;

    /**
     * Create a new RequestManager for OpenFGA API communication.
     *
     * Constructs a request manager with the specified configuration for communicating
     * with the OpenFGA API. PSR components can be explicitly provided for testing
     * or specific HTTP client requirements, or left null for automatic discovery.
     *
     * @param string                        $url                 The base URL for the OpenFGA API endpoint
     * @param int                           $maxRetries          Maximum number of retry attempts for failed requests
     * @param string|null                   $authorizationHeader Optional Authorization header value for API authentication
     * @param ClientInterface|null          $httpClient          Optional PSR-18 HTTP client, auto-discovered if not provided
     * @param ResponseFactoryInterface|null $httpResponseFactory Optional PSR-17 response factory, auto-discovered if not provided
     * @param StreamFactoryInterface|null   $httpStreamFactory   Optional PSR-17 stream factory, auto-discovered if not provided
     * @param RequestFactoryInterface|null  $httpRequestFactory  Optional PSR-17 request factory, auto-discovered if not provided
     * @param CircuitBreakerInterface|null  $circuitBreaker      Optional circuit breaker implementation, creates default if not provided
     * @param RetryHandlerInterface|null    $retryHandler        Optional retry handler implementation, creates default if not provided
     * @param ?TelemetryInterface           $telemetry
     */
    public function __construct(
        private readonly string $url,
        private readonly int $maxRetries,
        private readonly ?string $authorizationHeader = null,
        private ?ClientInterface $httpClient = null,
        private ?ResponseFactoryInterface $httpResponseFactory = null,
        private ?StreamFactoryInterface $httpStreamFactory = null,
        private ?RequestFactoryInterface $httpRequestFactory = null,
        ?CircuitBreakerInterface $circuitBreaker = null,
        ?RetryHandlerInterface $retryHandler = null,
        private readonly ?TelemetryInterface $telemetry = null,
    ) {
        $this->circuitBreaker = $circuitBreaker ?? new CircuitBreaker;
        $this->retryHandler = $retryHandler ?? new RetryHandler($this->circuitBreaker, $this->maxRetries);
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public static function handleResponseException(
        ResponseInterface $response,
        ?RequestInterface $request = null,
    ): never {
        $handles = [
            400 => NetworkError::Invalid,
            401 => NetworkError::Unauthenticated,
            403 => NetworkError::Forbidden,
            404 => NetworkError::UndefinedEndpoint,
            409 => NetworkError::Conflict,
            422 => NetworkError::Timeout,
            500 => NetworkError::Server,
        ];

        if (isset($handles[$response->getStatusCode()])) {
            $error = self::parseError($response);

            throw $handles[$response->getStatusCode()]->exception(request: $request, response: $response, context: ['%error%' => $error]);
        }

        throw NetworkError::Unexpected->exception(request: $request, response: $response, context: ['%error%' => Translator::trans(Messages::NETWORK_UNEXPECTED_STATUS, ['status_code' => $response->getStatusCode()])]);
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function getHttpClient(): ClientInterface
    {
        if (! $this->httpClient instanceof ClientInterface) {
            $httpClient = Discover::httpClient();

            if (null === $httpClient) {
                throw ConfigurationError::HttpClientMissing->exception();
            }

            $this->httpClient = $httpClient;
        }

        return $this->httpClient;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function getHttpRequestFactory(): RequestFactoryInterface
    {
        if (! $this->httpRequestFactory instanceof RequestFactoryInterface) {
            $httpRequestFactory = Discover::httpRequestFactory();

            if (null === $httpRequestFactory) {
                throw ConfigurationError::HttpRequestFactoryMissing->exception();
            }

            $this->httpRequestFactory = $httpRequestFactory;
        }

        return $this->httpRequestFactory;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function getHttpResponseFactory(): ResponseFactoryInterface
    {
        if (! $this->httpResponseFactory instanceof ResponseFactoryInterface) {
            $httpResponseFactory = Discover::httpResponseFactory();

            if (null === $httpResponseFactory) {
                throw ConfigurationError::HttpResponseFactoryMissing->exception();
            }

            $this->httpResponseFactory = $httpResponseFactory;
        }

        return $this->httpResponseFactory;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function getHttpStreamFactory(): StreamFactoryInterface
    {
        if (! $this->httpStreamFactory instanceof StreamFactoryInterface) {
            $httpStreamFactory = Discover::httpStreamFactory();

            if (null === $httpStreamFactory) {
                throw ConfigurationError::HttpStreamFactoryMissing->exception();
            }

            $this->httpStreamFactory = $httpStreamFactory;
        }

        return $this->httpStreamFactory;
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException If exception location capture fails
     */
    #[Override]
    public function request(ClientRequestInterface $request): RequestInterface
    {
        $request = $request->getRequest($this->getHttpStreamFactory());

        $method = $request->getMethod();
        $uri = $request->getUrl();
        $headers = $request->getHeaders();
        $body = $request->getBody();

        if ($request->useApiUrl()) {
            $uri = $this->url . '/' . trim($uri, '/');
        }

        $headers['User-Agent'] = sprintf('openfga-sdk php/%s', Client::VERSION);
        $headers['Content-Type'] ??= 'application/json';

        if (null !== $this->authorizationHeader) {
            $headers['Authorization'] = $this->authorizationHeader;
        }

        $request = $this->getHttpRequestFactory()->createRequest(
            method: $method->value,
            uri: $uri,
        );

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if ($body instanceof StreamInterface) {
            return $request->withBody($body);
        }

        return $request;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function send(RequestInterface $request): ResponseInterface
    {
        // Extract endpoint URL for circuit breaker tracking
        $endpoint = $request->getUri()->__toString();

        // Skip retry logic if maxRetries is 0
        if (0 === $this->maxRetries) {
            /** @var mixed $span */
            $span = $this->telemetry?->startHttpRequest($request);

            try {
                $response = $this->getHttpClient()->sendRequest($request);
                $this->telemetry?->endHttpRequest($span, $response);

                return $response;
            } catch (Throwable $throwable) {
                $this->telemetry?->endHttpRequest($span, null, $throwable);

                throw NetworkError::Request->exception(request: $request, context: ['message' => Translator::trans(Messages::NETWORK_ERROR, ['message' => $throwable->getMessage()])], prev: $throwable);
            }
        }

        // Use retry handler for requests with retry enabled
        try {
            return $this->retryHandler->executeWithRetry(
                fn (): ResponseInterface => $this->executeRequest($request),
                $request,
                $endpoint,
            );
        } catch (NetworkException $networkException) {
            // Preserve the response from the original NetworkException if available
            throw NetworkError::Request->exception(request: $request, response: $networkException->response(), context: ['message' => Translator::trans(Messages::NETWORK_ERROR, ['message' => $networkException->getMessage()])], prev: $networkException);
        } catch (Throwable $throwable) {
            throw NetworkError::Request->exception(request: $request, context: ['message' => Translator::trans(Messages::NETWORK_ERROR, ['message' => $throwable->getMessage()])], prev: $throwable);
        }
    }

    /**
     * Parse error information from an HTTP error response.
     *
     * Extracts and processes error information from the response body of failed
     * HTTP requests. This method attempts to parse JSON error responses from the
     * OpenFGA API and falls back to raw response content or generic error messages
     * when structured error data is unavailable.
     *
     * The method handles various error response formats:
     * - JSON responses with string error messages
     * - JSON responses with structured error objects
     * - Plain text error responses
     * - Empty or malformed response bodies
     *
     * This error information is used to provide meaningful error messages in
     * exception contexts, helping developers understand and resolve API issues.
     *
     * @param  ResponseInterface $response The HTTP error response to parse
     * @return string            A human-readable error message extracted from the response, or 'Unknown error' if no error information is available
     */
    private static function parseError(ResponseInterface $response): string
    {
        $error = '';

        try {
            $error = trim((string) $response->getBody());

            /** @var mixed $decoded */
            $decoded = json_decode($error, true, 512, JSON_THROW_ON_ERROR);

            return is_string($decoded) ? $decoded : $error;
        } catch (Exception) {
            if ('' !== $error) {
                return $error;
            }
        }

        return 'Unknown error';
    }

    /**
     * Execute a single HTTP request without retry logic.
     *
     * Performs the actual HTTP request using the configured PSR-18 client.
     * This method is called by the retry handler for each attempt.
     *
     * @param RequestInterface $request The HTTP request to execute
     *
     * @throws Throwable When the HTTP request fails
     *
     * @return ResponseInterface The HTTP response
     */
    private function executeRequest(RequestInterface $request): ResponseInterface
    {
        /** @var mixed $span */
        $span = $this->telemetry?->startHttpRequest($request);

        try {
            $response = $this->getHttpClient()->sendRequest($request);
            $this->telemetry?->endHttpRequest($span, $response);

            return $response;
        } catch (Throwable $throwable) {
            $this->telemetry?->endHttpRequest($span, null, $throwable);

            throw $throwable;
        }
    }
}
