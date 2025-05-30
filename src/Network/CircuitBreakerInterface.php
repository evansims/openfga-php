<?php

declare(strict_types=1);

namespace OpenFGA\Network;

/**
 * Circuit breaker interface for preventing cascade failures in distributed systems.
 *
 * This interface defines the contract for circuit breaker implementations that
 * temporarily disable requests to failing endpoints, preventing resource exhaustion
 * and allowing time for recovery. Circuit breakers track failures per endpoint and
 * automatically open/close based on failure thresholds and cooldown periods.
 *
 * The circuit breaker operates in three states:
 * - Closed: Normal operation, requests are allowed
 * - Open: Failures exceeded threshold, requests are blocked
 * - Half-Open: After cooldown, limited requests allowed to test recovery
 *
 * @see https://martinfowler.com/bliki/CircuitBreaker.html Circuit Breaker Pattern
 * @see https://openfga.dev/docs/getting-started/setup-sdk-client OpenFGA SDK Configuration
 */
interface CircuitBreakerInterface
{
    /**
     * Get the current failure count for an endpoint.
     *
     * Returns the number of consecutive failures recorded for the specified
     * endpoint. This can be useful for logging and monitoring purposes.
     *
     * @param  string $endpoint The endpoint URL or identifier to check
     * @return int    The current failure count (0 if no failures recorded)
     */
    public function getFailureCount(string $endpoint): int;

    /**
     * Check if the circuit is currently open for an endpoint.
     *
     * Returns true if the circuit breaker is currently blocking requests
     * to the specified endpoint due to excessive failures.
     *
     * @param  string $endpoint The endpoint URL or identifier to check
     * @return bool   True if the circuit is open (blocking requests), false otherwise
     */
    public function isOpen(string $endpoint): bool;

    /**
     * Record a failure for the specified endpoint.
     *
     * Increments the failure count for the endpoint and updates the failure timestamp.
     * If the failure threshold is reached, the circuit will open and block subsequent
     * requests until the cooldown period expires.
     *
     * @param string $endpoint The endpoint URL or identifier that failed
     */
    public function recordFailure(string $endpoint): void;

    /**
     * Record a successful request for the specified endpoint.
     *
     * Resets the failure state for the endpoint, effectively closing the circuit
     * and allowing normal operation to resume. This should be called whenever
     * a request succeeds after previous failures.
     *
     * @param string $endpoint The endpoint URL or identifier that succeeded
     */
    public function recordSuccess(string $endpoint): void;

    /**
     * Check if the circuit breaker should allow a request to the specified endpoint.
     *
     * Evaluates whether a request should be allowed based on the current circuit
     * state for the given endpoint. If the cooldown period has passed, the circuit
     * is automatically reset to allow new attempts.
     *
     * @param  string $endpoint The endpoint URL or identifier to check
     * @return bool   True if requests should be allowed, false if the circuit is open
     */
    public function shouldRetry(string $endpoint): bool;
}
