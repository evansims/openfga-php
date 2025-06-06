<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use const CASE_LOWER;

use OpenFGA\{Exceptions\NetworkError, Exceptions\NetworkException};
use Override;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

use function array_change_key_case;
use function in_array;
use function is_numeric;
use function max;
use function min;
use function mt_rand;
use function strtotime;
use function time;

/**
 * Abstract retry handler with exponential backoff, jitter, and circuit breaker integration.
 *
 * This abstract class implements a sophisticated retry strategy for HTTP requests, providing:
 * - Exponential backoff with configurable jitter to prevent thundering herd
 * - Respect for server-provided retry timing via Retry-After and rate limit headers
 * - Circuit breaker integration to prevent cascade failures
 * - Context-aware retry limits based on operation type
 * - Comprehensive logging and monitoring support
 *
 * The retry handler categorizes errors and applies appropriate retry strategies:
 * - Network errors: Fast initial retry with exponential backoff
 * - Rate limits (429): Honor server timing headers exactly
 * - Server errors (5xx): Standard exponential backoff
 * - Maintenance (503): Extended delays for service recovery
 *
 * @see https://aws.amazon.com/builders-library/timeouts-retries-and-backoff-with-jitter/ AWS Retry Guidelines
 */
abstract readonly class AbstractRetryHandler implements RetryHandlerInterface
{
    /**
     * Base delay for exponential backoff in milliseconds.
     */
    private const int BASE_DELAY_MS = 100;

    /**
     * Default maximum number of retry attempts.
     */
    private const int DEFAULT_MAX_RETRIES = 3;

    /**
     * Fast retry delay for network connection issues in milliseconds.
     */
    private const int FAST_RETRY_DELAY_MS = 50;

    /**
     * HTTP methods that are considered idempotent and safe to retry.
     *
     * @var array<string>
     */
    private const array IDEMPOTENT_METHODS = ['GET', 'HEAD', 'PUT', 'DELETE', 'OPTIONS', 'TRACE'];

    /**
     * Jitter factor for randomizing delays (±25%).
     */
    private const float JITTER_FACTOR = 0.25;

    /**
     * Extended delay for maintenance/service unavailable responses in milliseconds.
     */
    private const int MAINTENANCE_DELAY_MS = 5000;

    /**
     * Maximum delay between retry attempts in milliseconds.
     */
    private const int MAX_DELAY_MS = 2000;

    /**
     * HTTP status codes that should trigger retry attempts.
     *
     * @var array<int>
     */
    private const array RETRYABLE_STATUS_CODES = [429, 502, 503, 504];

    /**
     * Create a new retry handler with circuit breaker integration.
     *
     * @param CircuitBreakerInterface $circuitBreaker The circuit breaker for tracking endpoint health
     * @param int                     $maxRetries     Maximum number of retry attempts (defaults to 3)
     */
    public function __construct(
        private CircuitBreakerInterface $circuitBreaker,
        private int $maxRetries = self::DEFAULT_MAX_RETRIES,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function executeWithRetry(callable $requestExecutor, RequestInterface $request, string $endpoint): ResponseInterface
    {
        // Check circuit breaker before attempting request
        if (! $this->circuitBreaker->shouldRetry($endpoint)) {
            throw new NetworkException(kind: NetworkError::Unexpected, request: $request, context: ['message' => 'Circuit breaker is open for endpoint']);
        }

        $attempt = 0;

        while ($attempt <= $this->maxRetries) {
            try {
                $response = $requestExecutor();

                // Evaluate response for retry eligibility
                if ($this->shouldRetryResponse($response, $request, $attempt)) {
                    $delay = $this->calculateResponseDelay($response, $attempt);

                    if ($attempt < $this->maxRetries) {
                        $this->sleep($delay);
                    }
                } else {
                    // Success or non-retryable response
                    $statusCode = $response->getStatusCode();

                    if (200 <= $statusCode && 300 > $statusCode) {
                        $this->circuitBreaker->recordSuccess($endpoint);

                        return $response;
                    }

                    // Non-retryable error response
                    $statusCode = $response->getStatusCode();
                    $errorKind = match ($statusCode) {
                        400 => NetworkError::Invalid,
                        401 => NetworkError::Unauthenticated,
                        403 => NetworkError::Forbidden,
                        404 => NetworkError::UndefinedEndpoint,
                        409 => NetworkError::Conflict,
                        422 => NetworkError::Timeout,
                        500 => NetworkError::Server,
                        default => NetworkError::Unexpected,
                    };

                    throw new NetworkException(kind: $errorKind, request: $request, response: $response);
                }
            } catch (NetworkExceptionInterface $networkException) {
                // Handle network-level exceptions (connection errors, timeouts)
                if (! $this->shouldRetryNetworkError($request, $attempt)) {
                    $this->circuitBreaker->recordFailure($endpoint);

                    throw $networkException;
                }

                // Use fast retry for network issues
                $delay = $this->calculateNetworkErrorDelay($attempt);

                if ($attempt < $this->maxRetries) {
                    $this->sleep($delay);
                }
            } catch (Throwable $exception) {
                // Non-network exceptions are generally not retryable
                $this->circuitBreaker->recordFailure($endpoint);

                throw $exception;
            }

            ++$attempt;
        }

        // All retries exhausted
        $this->circuitBreaker->recordFailure($endpoint);

        throw new NetworkException(kind: NetworkError::Unexpected, request: $request, context: ['message' => 'Maximum retry attempts exceeded']);
    }

    /**
     * Suspends execution for the calculated delay period.
     *
     * This abstract method allows subclasses to implement their own sleep mechanism,
     * which is particularly useful for testing with reduced delays.
     *
     * @param int $delayMs Delay duration in milliseconds
     */
    abstract protected function sleep(int $delayMs): void;

    /**
     * Get the base delay for exponential backoff in milliseconds.
     *
     * @return int Base delay in milliseconds
     */
    protected function getBaseDelayMs(): int
    {
        return self::BASE_DELAY_MS;
    }

    /**
     * Get the fast retry delay for network errors in milliseconds.
     *
     * @return int Fast retry delay in milliseconds
     */
    protected function getFastRetryDelayMs(): int
    {
        return self::FAST_RETRY_DELAY_MS;
    }

    /**
     * Get the jitter factor for delay randomization.
     *
     * @return float Jitter factor (0.0 to 1.0)
     */
    protected function getJitterFactor(): float
    {
        return self::JITTER_FACTOR;
    }

    /**
     * Get the maintenance delay for 503 status codes in milliseconds.
     *
     * @return int Maintenance delay in milliseconds
     */
    protected function getMaintenanceDelayMs(): int
    {
        return self::MAINTENANCE_DELAY_MS;
    }

    /**
     * Get the maximum delay between retries in milliseconds.
     *
     * @return int Maximum delay in milliseconds
     */
    protected function getMaxDelayMs(): int
    {
        return self::MAX_DELAY_MS;
    }

    /**
     * Calculate exponential backoff delay with jitter.
     *
     * Implements exponential backoff with randomized jitter to prevent
     * thundering herd scenarios in distributed systems.
     *
     * @param  int $attempt Current attempt number (0-indexed)
     * @return int Delay in milliseconds
     *
     * @psalm-return int<min, max>
     */
    private function calculateExponentialBackoff(int $attempt): int
    {
        $baseDelay = min($this->getBaseDelayMs() * (int) (2 ** $attempt), $this->getMaxDelayMs());
        $jitter = (int) ((float) $baseDelay * $this->getJitterFactor());

        return $baseDelay + mt_rand(-$jitter, $jitter);
    }

    /**
     * Calculate delay for network error retries.
     *
     * Network errors typically benefit from faster initial retries
     * before falling back to exponential backoff for subsequent attempts.
     *
     * @param  int $attempt Current attempt number (0-indexed)
     * @return int Delay in milliseconds before next retry attempt
     */
    private function calculateNetworkErrorDelay(int $attempt): int
    {
        // Fast retry for first network error attempt
        if (0 === $attempt) {
            return $this->getFastRetryDelayMs();
        }

        // Standard exponential backoff for subsequent attempts
        return $this->calculateExponentialBackoff($attempt);
    }

    /**
     * Calculate retry delay based on HTTP response headers and attempt number.
     *
     * Implements priority-based delay calculation:
     * 1. Retry-After header (highest priority)
     * 2. X-Rate-Limit-Reset header
     * 3. Exponential backoff with jitter (fallback)
     *
     * @param  ResponseInterface $response The HTTP response containing delay headers
     * @param  int               $attempt  Current attempt number (0-indexed)
     * @return int               Delay in milliseconds before next retry attempt
     */
    private function calculateResponseDelay(ResponseInterface $response, int $attempt): int
    {
        $headers = array_change_key_case($response->getHeaders(), CASE_LOWER);

        // Priority 1: Retry-After header (RFC 7231)
        if (isset($headers['retry-after'][0])) {
            return $this->parseRetryAfter($headers['retry-after'][0]);
        }

        // Priority 2: X-Rate-Limit-Reset header (common rate limit pattern)
        if (isset($headers['x-rate-limit-reset'][0])) {
            $resetTime = (int) $headers['x-rate-limit-reset'][0];
            $delay = max(0, ($resetTime - time()) * 1000);

            if (0 < $delay) {
                return $delay;
            }
        }

        // Priority 3: Special handling for maintenance status
        $statusCode = $response->getStatusCode();

        if (503 === $statusCode) {
            // Service unavailable - use longer delay
            return $this->getMaintenanceDelayMs();
        }

        // Default: Exponential backoff with jitter
        return $this->calculateExponentialBackoff($attempt);
    }

    /**
     * Parse Retry-After header value.
     *
     * Handles both delay-seconds and HTTP-date formats as specified in RFC 7231.
     * Converts the value to milliseconds for consistent delay handling.
     *
     * @param  string $retryAfter The Retry-After header value
     * @return int    Delay in milliseconds
     */
    private function parseRetryAfter(string $retryAfter): int
    {
        // Try parsing as seconds (numeric value)
        if (is_numeric($retryAfter)) {
            return max(0, (int) $retryAfter * 1000);
        }

        // Try parsing as HTTP date
        $timestamp = strtotime($retryAfter);

        if (false !== $timestamp) {
            return max(0, ($timestamp - time()) * 1000);
        }

        // If parsing fails, use default exponential backoff
        return $this->calculateExponentialBackoff(0);
    }

    /**
     * Determine if a network error should trigger a retry attempt.
     *
     * Evaluates network-level exceptions to determine retry eligibility.
     * Network errors are generally retryable unless they indicate
     * permanent connectivity issues.
     *
     * @param  RequestInterface $request The original request for context
     * @param  int              $attempt Current attempt number (0-indexed)
     * @return bool             True if the request should be retried, false otherwise
     */
    private function shouldRetryNetworkError(RequestInterface $request, int $attempt): bool
    {
        // Don't retry if we've reached max attempts
        if ($attempt >= $this->maxRetries) {
            return false;
        }

        // For non-idempotent methods, be more conservative with network retries
        if (! in_array($request->getMethod(), self::IDEMPOTENT_METHODS, true)) {
            // Only retry the first attempt for non-idempotent methods
            return 0 === $attempt;
        }

        return true;
    }

    /**
     * Determine if an HTTP response should trigger a retry attempt.
     *
     * Evaluates the HTTP response status code and request method to determine
     * if the request should be retried. Considers idempotency requirements
     * and server response codes.
     *
     * @param  ResponseInterface $response The HTTP response to evaluate
     * @param  RequestInterface  $request  The original request for context
     * @param  int               $attempt  Current attempt number (0-indexed)
     * @return bool              True if the request should be retried, false otherwise
     */
    private function shouldRetryResponse(ResponseInterface $response, RequestInterface $request, int $attempt): bool
    {
        $statusCode = $response->getStatusCode();

        // Don't retry if we've reached max attempts
        if ($attempt >= $this->maxRetries) {
            return false;
        }

        // Only retry specific status codes
        if (! in_array($statusCode, self::RETRYABLE_STATUS_CODES, true)) {
            return false;
        }

        // For non-idempotent methods, only retry on specific conditions
        if (! in_array($request->getMethod(), self::IDEMPOTENT_METHODS, true)) {
            // Only retry 429 (rate limit) for non-idempotent methods
            return 429 === $statusCode;
        }

        return true;
    }
}
