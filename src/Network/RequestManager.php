<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use const JSON_THROW_ON_ERROR;

use Exception;
use Fiber;
use InvalidArgumentException;
use OpenFGA\{Client, Messages};
use OpenFGA\Exceptions\{ConfigurationError, NetworkError, NetworkException};
use OpenFGA\Observability\TelemetryInterface;
use OpenFGA\Requests\RequestInterface as ClientRequestInterface;
use OpenFGA\Results\{Failure, FailureInterface, Success, SuccessInterface};
use OpenFGA\Services\AuthenticationServiceInterface;
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface, ResponseFactoryInterface, ResponseInterface, StreamFactoryInterface, StreamInterface};
use PsrDiscovery\Discover;
use ReflectionException;
use Throwable;

use function count;
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
     * Concurrent executor for parallel task execution.
     */
    private readonly ConcurrentExecutorInterface $concurrentExecutor;

    /**
     * HTTP client wrapper for making requests.
     */
    private readonly HttpClientInterface $httpClientWrapper;

    /**
     * Retry strategy for handling failures.
     */
    private readonly RetryStrategyInterface $retryStrategy;

    /**
     * Create a new RequestManager for OpenFGA API communication.
     *
     * Constructs a request manager with the specified configuration for communicating
     * with the OpenFGA API. PSR components can be explicitly provided for testing
     * or specific HTTP client requirements, or left null for automatic discovery.
     *
     * @param string                              $url                   The base URL for the OpenFGA API endpoint
     * @param int                                 $maxRetries            Maximum number of retry attempts for failed requests
     * @param string|null                         $authorizationHeader   Optional Authorization header value for API authentication (deprecated - use authenticationService)
     * @param ClientInterface|null                $httpClient            Optional PSR-18 HTTP client, auto-discovered if not provided
     * @param ResponseFactoryInterface|null       $httpResponseFactory   Optional PSR-17 response factory, auto-discovered if not provided
     * @param StreamFactoryInterface|null         $httpStreamFactory     Optional PSR-17 stream factory, auto-discovered if not provided
     * @param RequestFactoryInterface|null        $httpRequestFactory    Optional PSR-17 request factory, auto-discovered if not provided
     * @param ?TelemetryInterface                 $telemetry
     * @param HttpClientInterface|null            $httpClientWrapper     Optional HTTP client wrapper
     * @param RetryStrategyInterface|null         $retryStrategy         Optional retry strategy
     * @param ConcurrentExecutorInterface|null    $concurrentExecutor    Optional concurrent executor
     * @param AuthenticationServiceInterface|null $authenticationService Optional authentication service for dynamic header resolution
     */
    public function __construct(
        private readonly string $url,
        private readonly int $maxRetries,
        private readonly ?string $authorizationHeader = null,
        private ?ClientInterface $httpClient = null,
        private ?ResponseFactoryInterface $httpResponseFactory = null,
        private ?StreamFactoryInterface $httpStreamFactory = null,
        private ?RequestFactoryInterface $httpRequestFactory = null,
        private readonly ?TelemetryInterface $telemetry = null,
        ?HttpClientInterface $httpClientWrapper = null,
        ?RetryStrategyInterface $retryStrategy = null,
        ?ConcurrentExecutorInterface $concurrentExecutor = null,
        private readonly ?AuthenticationServiceInterface $authenticationService = null,
    ) {
        // Initialize new interface implementations
        $this->httpClientWrapper = $httpClientWrapper ?? new PsrHttpClient($this->httpClient);
        $this->retryStrategy = $retryStrategy ?? new ExponentialBackoffRetryStrategy($this->maxRetries);
        $this->concurrentExecutor = $concurrentExecutor ?? new FiberConcurrentExecutor;
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
     * Execute multiple tasks concurrently using Fibers.
     *
     * This method creates and manages Fibers for concurrent execution of the
     * provided tasks. It respects the maximum parallelism limit and efficiently
     * schedules fiber execution to maximize throughput.
     *
     * @param array<callable(): (FailureInterface|SuccessInterface)> $tasks               Array of tasks to execute concurrently
     * @param int                                                    $maxParallelRequests Maximum concurrent requests
     * @param bool                                                   $stopOnFirstError    Whether to stop on first error
     *
     * @throws Throwable If task execution fails
     *
     * @return array<FailureInterface|SuccessInterface> Results from all tasks in the same order as input
     */
    public function executeParallel(array $tasks, int $maxParallelRequests, bool $stopOnFirstError): array
    {
        if ([] === $tasks) {
            return [];
        }

        // If no parallelism requested or only one task, execute sequentially
        if (1 >= $maxParallelRequests || 1 === count($tasks) || ! $this->concurrentExecutor->supportsConcurrency()) {
            return $this->executeSequential($tasks, $stopOnFirstError);
        }

        // Wrap tasks to handle Result types if stopOnFirstError is enabled
        if ($stopOnFirstError) {
            return $this->executeConcurrentWithEarlyStop($tasks, $maxParallelRequests);
        }

        // Use the concurrent executor for parallel execution
        /** @var array<int, callable(): (FailureInterface|SuccessInterface)> $tasks */
        $results = $this->concurrentExecutor->executeParallel($tasks, $maxParallelRequests);

        // Convert any Throwables to Failure results
        return array_map(static function ($result): FailureInterface | SuccessInterface {
            if ($result instanceof Throwable) {
                return new Failure($result);
            }

            return $result;
        }, $results);
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
     * @throws Throwable           If authentication header retrieval fails
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

        // Try to get authorization header from authentication service first, then fall back to static header
        $authHeader = null;

        if ($this->authenticationService instanceof AuthenticationServiceInterface) {
            $authHeader = $this->authenticationService->getAuthorizationHeader($this->getHttpStreamFactory());
        }

        if (null !== $authHeader) {
            $headers['Authorization'] = $authHeader;
        } elseif (null !== $this->authorizationHeader) {
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
     * @throws Throwable                If the request fails and cannot be recovered
     */
    #[Override]
    public function send(RequestInterface $request): ResponseInterface
    {
        // Create a single-task array for unified execution
        $task = function () use ($request): FailureInterface | SuccessInterface {
            try {
                $response = $this->executeSingleRequest($request);

                return new Success($response);
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        };

        // Execute using the unified concurrent infrastructure with concurrency of 1
        $results = $this->executeParallel([$task], 1, true);

        // Extract the single result
        if (! isset($results[0])) {
            throw new NetworkException(kind: NetworkError::Unexpected, context: ['message' => 'No result returned from request execution']);
        }

        $result = $results[0];

        if ($result instanceof FailureInterface) {
            throw $result->err();
        }

        /** @var ResponseInterface */
        return $result->unwrap();
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
     * Execute tasks concurrently using Fibers.
     *
     * @param  array<callable(): (FailureInterface|SuccessInterface)> $tasks            Array of tasks to execute
     * @param  int                                                    $maxParallel      Maximum concurrent executions
     * @param  bool                                                   $stopOnFirstError Whether to stop on first error
     * @return array<FailureInterface|SuccessInterface>
     */
    private function executeConcurrent(array $tasks, int $maxParallel, bool $stopOnFirstError): array
    {
        $results = [];
        $activeFibers = [];
        $taskIndex = 0;
        $totalTasks = count($tasks);
        $shouldStop = false;

        // Initialize results array with placeholders
        for ($i = 0; $i < $totalTasks; ++$i) {
            $results[$i] = null;
        }

        while ($taskIndex < $totalTasks || [] !== $activeFibers) {
            // Start new fibers up to the maximum parallel limit
            while (count($activeFibers) < $maxParallel && $taskIndex < $totalTasks && ! $shouldStop) {
                $currentIndex = $taskIndex;
                $task = $tasks[$taskIndex];

                $fiber = new Fiber(static function () use ($task): FailureInterface | SuccessInterface {
                    try {
                        return $task();
                    } catch (Throwable $throwable) {
                        return new Failure($throwable);
                    }
                });

                $activeFibers[$currentIndex] = $fiber;
                $fiber->start();
                ++$taskIndex;
            }

            // Check for completed fibers
            foreach ($activeFibers as $index => $fiber) {
                if ($fiber->isTerminated()) {
                    /** @var FailureInterface|SuccessInterface $result */
                    $result = $fiber->getReturn();
                    $results[$index] = $result;
                    unset($activeFibers[$index]);

                    // Check if we should stop on first error
                    if ($stopOnFirstError && $result instanceof FailureInterface) {
                        $shouldStop = true;

                        // Terminate remaining active fibers
                        foreach ($activeFibers as $activeFiber) {
                            if ($activeFiber->isSuspended()) {
                                $activeFiber->resume();
                            }
                        }
                        $activeFibers = [];

                        break;
                    }
                } elseif ($fiber->isSuspended()) {
                    // Resume suspended fibers
                    $fiber->resume();
                }
            }

            // Yield control to allow other operations
            if ([] !== $activeFibers) {
                Fiber::suspend();
            }
        }

        // Filter out null results (from stopped executions)
        /** @var array<FailureInterface|SuccessInterface> $filteredResults */
        $filteredResults = array_filter($results, static fn ($result): bool => null !== $result);

        return array_values($filteredResults);
    }

    /**
     * Execute tasks concurrently with early stopping on first error.
     *
     * This method is a specialized version of concurrent execution that
     * stops all remaining tasks when the first error is encountered.
     *
     * @param  array<callable(): (FailureInterface|SuccessInterface)> $tasks       Array of tasks to execute
     * @param  int                                                    $maxParallel Maximum concurrent executions
     * @return array<FailureInterface|SuccessInterface>
     */
    private function executeConcurrentWithEarlyStop(array $tasks, int $maxParallel): array
    {
        // For now, use the original executeConcurrent method since it already handles stopOnFirstError
        // In the future, this could be optimized further with the ConcurrentExecutorInterface
        return $this->executeConcurrent($tasks, $maxParallel, true);
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
        $span = $this->telemetry?->startHttpRequest($request);

        try {
            $response = $this->httpClientWrapper->send($request);
            $this->telemetry?->endHttpRequest($span, $response);

            return $response;
        } catch (Throwable $throwable) {
            $this->telemetry?->endHttpRequest($span, null, $throwable);

            throw $throwable;
        }
    }

    /**
     * Execute tasks sequentially without Fibers.
     *
     * @param  array<callable(): (FailureInterface|SuccessInterface)> $tasks            Array of tasks to execute
     * @param  bool                                                   $stopOnFirstError Whether to stop on first error
     * @return array<FailureInterface|SuccessInterface>
     */
    private function executeSequential(array $tasks, bool $stopOnFirstError): array
    {
        $results = [];

        foreach ($tasks as $task) {
            try {
                $result = $task();
                $results[] = $result;

                // Stop if we encounter an error and stopOnFirstError is enabled
                if ($stopOnFirstError && $result instanceof FailureInterface) {
                    break;
                }
            } catch (Throwable $throwable) {
                $results[] = new Failure($throwable);

                if ($stopOnFirstError) {
                    break;
                }
            }
        }

        return $results;
    }

    /**
     * Execute a single HTTP request with retry logic.
     *
     * Handles the execution of a single HTTP request, applying retry logic
     * based on the configured maxRetries setting. This method provides a
     * unified execution path for both single and concurrent requests.
     *
     * @param RequestInterface $request The HTTP request to execute
     *
     * @throws NetworkException If the request fails after all retries
     * @throws Throwable        For any other errors during execution
     *
     * @return ResponseInterface The HTTP response
     */
    private function executeSingleRequest(RequestInterface $request): ResponseInterface
    {
        $endpoint = $request->getUri()->__toString();

        // Direct execution without retry if maxRetries is 0
        if (0 === $this->maxRetries) {
            $span = $this->telemetry?->startHttpRequest($request);

            try {
                $response = $this->httpClientWrapper->send($request);
                $this->telemetry?->endHttpRequest($span, $response);

                return $response;
            } catch (Throwable $throwable) {
                $this->telemetry?->endHttpRequest($span, null, $throwable);

                throw NetworkError::Request->exception(request: $request, context: ['message' => Translator::trans(Messages::NETWORK_ERROR, ['message' => $throwable->getMessage()])], prev: $throwable);
            }
        }

        // Use new retry strategy for requests with retry enabled
        try {
            return $this->retryStrategy->execute(
                fn (): ResponseInterface => $this->executeRequest($request),
                [
                    'request' => $request,
                    'endpoint' => $endpoint,
                ],
            );
        } catch (NetworkException $networkException) {
            // Preserve the response from the original NetworkException if available
            throw NetworkError::Request->exception(request: $request, response: $networkException->response(), context: ['message' => Translator::trans(Messages::NETWORK_ERROR, ['message' => $networkException->getMessage()])], prev: $networkException);
        } catch (Throwable $throwable) {
            throw NetworkError::Request->exception(request: $request, context: ['message' => Translator::trans(Messages::NETWORK_ERROR, ['message' => $throwable->getMessage()])], prev: $throwable);
        }
    }
}
