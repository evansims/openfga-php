<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

/**
 * Retry handler interface for advanced HTTP request retry strategies.
 *
 * This interface defines the contract for retry handler implementations that
 * provide sophisticated retry logic for HTTP requests, including exponential
 * backoff, jitter, circuit breaker integration, and server-header-aware delays.
 *
 * Retry handlers categorize errors and apply appropriate retry strategies:
 * - Network errors: Fast initial retry with exponential backoff
 * - Rate limits (429): Honor server timing headers exactly
 * - Server errors (5xx): Standard exponential backoff
 * - Maintenance (503): Extended delays for service recovery
 *
 * The implementation should respect server-provided timing via Retry-After
 * and rate limit headers while providing fallback logic for cases where
 * such headers are unavailable.
 *
 * @see https://aws.amazon.com/builders-library/timeouts-retries-and-backoff-with-jitter/ AWS Retry Guidelines
 * @see https://openfga.dev/docs/getting-started/setup-sdk-client OpenFGA SDK Configuration
 */
interface RetryHandlerInterface
{
    /**
     * Execute an HTTP request with automatic retry logic.
     *
     * Performs the HTTP request with intelligent retry behavior based on error type,
     * server headers, and circuit breaker state. The method tracks attempt counts,
     * calculates appropriate delays, and respects server-provided timing information.
     *
     * The implementation should:
     * - Check circuit breaker state before attempting requests
     * - Apply exponential backoff with jitter to prevent thundering herd
     * - Respect server-provided timing headers (Retry-After, X-Rate-Limit-Reset)
     * - Handle different error types with appropriate retry strategies
     * - Consider request method idempotency for retry decisions
     * - Track failures and successes with the circuit breaker
     *
     * @param callable(): ResponseInterface $requestExecutor Function that executes the HTTP request
     * @param RequestInterface              $request         The original HTTP request for context
     * @param string                        $endpoint        The endpoint URL for circuit breaker tracking
     *
     * @throws Throwable When all retry attempts are exhausted or non-retryable errors occur
     *
     * @return ResponseInterface The successful HTTP response
     */
    public function executeWithRetry(
        callable $requestExecutor,
        RequestInterface $request,
        string $endpoint,
    ): ResponseInterface;
}
