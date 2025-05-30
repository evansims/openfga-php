<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use Override;

/**
 * Circuit breaker implementation for preventing cascade failures in distributed systems.
 *
 * This class implements the circuit breaker pattern to temporarily disable requests to
 * failing endpoints, preventing resource exhaustion and allowing time for recovery.
 * The circuit breaker tracks failures per endpoint and automatically opens/closes
 * based on failure thresholds and cooldown periods.
 *
 * The circuit breaker operates in three states:
 * - Closed: Normal operation, requests are allowed
 * - Open: Failures exceeded threshold, requests are blocked
 * - Half-Open: After cooldown, limited requests allowed to test recovery
 *
 * @see https://martinfowler.com/bliki/CircuitBreaker.html Circuit Breaker Pattern
 */
final class CircuitBreaker implements CircuitBreakerInterface
{
    /**
     * Cooldown period in seconds before allowing retry attempts.
     */
    private const int CIRCUIT_BREAKER_COOLDOWN = 30;

    /**
     * Number of consecutive failures before opening the circuit.
     */
    private const int CIRCUIT_BREAKER_THRESHOLD = 5;

    /**
     * Tracks failure counts per endpoint.
     *
     * @var array<string, int>
     */
    private array $failures = [];

    /**
     * Tracks the timestamp of the last failure per endpoint.
     *
     * @var array<string, int>
     */
    private array $lastFailureTime = [];

    /**
     * @inheritDoc
     */
    #[Override]
    public function getFailureCount(string $endpoint): int
    {
        $key = $this->getEndpointKey($endpoint);

        return $this->failures[$key] ?? 0;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function isOpen(string $endpoint): bool
    {
        return ! $this->shouldRetry($endpoint);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function recordFailure(string $endpoint): void
    {
        $key = $this->getEndpointKey($endpoint);
        $this->failures[$key] = ($this->failures[$key] ?? 0) + 1;
        $this->lastFailureTime[$key] = time();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function recordSuccess(string $endpoint): void
    {
        $key = $this->getEndpointKey($endpoint);
        unset($this->failures[$key], $this->lastFailureTime[$key]);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function shouldRetry(string $endpoint): bool
    {
        $key = $this->getEndpointKey($endpoint);

        // Reset if cooldown period passed
        if (isset($this->lastFailureTime[$key])
            && self::CIRCUIT_BREAKER_COOLDOWN < time() - $this->lastFailureTime[$key]) {
            unset($this->failures[$key], $this->lastFailureTime[$key]);
        }

        return ($this->failures[$key] ?? 0) < self::CIRCUIT_BREAKER_THRESHOLD;
    }

    /**
     * Generate a normalized key for endpoint tracking.
     *
     * Creates a consistent key for tracking endpoint state by extracting
     * the host and path components from URLs while ignoring query parameters
     * and fragments that don't affect endpoint identity.
     *
     * @param  string $endpoint The endpoint URL or identifier
     * @return string A normalized key for internal tracking
     */
    private function getEndpointKey(string $endpoint): string
    {
        // Parse URL to extract host and path for consistent keying
        $parsed = parse_url($endpoint);

        if (false === $parsed) {
            return $endpoint;
        }

        $host = $parsed['host'] ?? '';
        $path = $parsed['path'] ?? '';

        return $host . $path;
    }
}
