<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use OpenFGA\{Exceptions\NetworkError, Messages, Translation\Translator};
use Override;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use RuntimeException;
use Throwable;

use function in_array;
use function is_numeric;
use function max;
use function min;
use function mt_rand;
use function time;
use function usleep;

/**
 * Advanced retry handler with exponential backoff, jitter, and circuit breaker integration.
 *
 * This class implements a sophisticated retry strategy for HTTP requests, providing:
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
final readonly class RetryHandler implements RetryHandlerInterface
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

    public function __construct(
        private CircuitBreakerInterface $circuitBreaker,
        private int $maxRetries = self::DEFAULT_MAX_RETRIES,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function executeWithRetry(
        callable $requestExecutor,
        RequestInterface $request,
        string $endpoint,
    ): ResponseInterface {
        $attempt = 0;

        // Check circuit breaker before attempting any requests
        if (! $this->circuitBreaker->shouldRetry($endpoint)) {
            throw new RuntimeException(Translator::trans(Messages::NETWORK_ERROR, ['message' => 'Circuit breaker is open for endpoint: ' . $endpoint]));
        }

        while ($attempt <= $this->maxRetries) {
            try {
                $response = $requestExecutor();

                // Check if the response indicates success
                if (200 <= $response->getStatusCode() && 300 > $response->getStatusCode()) {
                    // Record success and return
                    $this->circuitBreaker->recordSuccess($endpoint);

                    return $response;
                }

                // Handle HTTP error responses
                if (! $this->shouldRetryResponse($response, $request, $attempt)) {
                    // Create a NetworkException that includes the response for debugging
                    throw NetworkError::Unexpected->exception(request: $request, response: $response, context: ['message' => Translator::trans(Messages::NETWORK_UNEXPECTED_STATUS, ['status_code' => $response->getStatusCode()])]);
                }

                // Calculate delay based on response headers and attempt number
                $delay = $this->calculateDelayFromResponse($response, $attempt);

                if ($attempt < $this->maxRetries) {
                    $this->sleep($delay);
                }
            } catch (NetworkExceptionInterface $exception) {
                // Network errors are generally retryable
                if (! $this->shouldRetryNetworkError($request, $attempt)) {
                    $this->circuitBreaker->recordFailure($endpoint);

                    throw $exception;
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

        throw new RuntimeException(Translator::trans(Messages::NETWORK_ERROR, ['message' => 'Maximum retry attempts exceeded']));
    }

    /**
     * Calculate retry delay based on HTTP response headers and attempt number.
     *
     * Implements priority-based delay calculation:
     * 1. Retry-After header (highest priority)
     * 2. X-Rate-Limit-Reset header
     * 3. Exponential backoff with jitter (fallback)
     *
     * @param  ResponseInterface $response The HTTP response containing headers
     * @param  int               $attempt  Current attempt number (0-indexed)
     * @return int               Delay in milliseconds before next retry attempt
     */
    private function calculateDelayFromResponse(ResponseInterface $response, int $attempt): int
    {
        $headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            $headers[strtolower((string) $name)] = $values[0] ?? '';
        }

        // Priority 1: Retry-After header
        if (isset($headers['retry-after'])) {
            return $this->parseRetryAfter($headers['retry-after']);
        }

        // Priority 2: X-Rate-Limit-Reset header
        if (isset($headers['x-rate-limit-reset'])) {
            $resetTime = (int) $headers['x-rate-limit-reset'];

            return max(0, ($resetTime - time()) * 1000);
        }

        // Priority 3: Status-specific delays
        $statusCode = $response->getStatusCode();
        if (503 === $statusCode) {
            // Service unavailable - use longer delay
            return self::MAINTENANCE_DELAY_MS;
        }

        // Default: Exponential backoff with jitter
        return $this->calculateExponentialBackoff($attempt);
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
        $baseDelay = min(self::BASE_DELAY_MS * (int) (2 ** $attempt), self::MAX_DELAY_MS);
        $jitter = (int) ((float) $baseDelay * self::JITTER_FACTOR);

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
            return self::FAST_RETRY_DELAY_MS;
        }

        // Standard exponential backoff for subsequent attempts
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
            // Only retry rate limits for non-idempotent methods
            return 429 === $statusCode;
        }

        return true;
    }

    /**
     * Sleep for the specified duration.
     *
     * Suspends execution for the calculated delay period. Uses microsleep
     * for precise timing control.
     *
     * @param int $delayMs Delay duration in milliseconds
     */
    private function sleep(int $delayMs): void
    {
        if (0 < $delayMs) {
            usleep($delayMs * 1000); // Convert to microseconds
        }
    }
}
