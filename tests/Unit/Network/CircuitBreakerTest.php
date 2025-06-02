<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Network;

use OpenFGA\Network\{CircuitBreaker, CircuitBreakerInterface};

describe('CircuitBreaker', function (): void {
    beforeEach(function (): void {
        $this->circuitBreaker = new CircuitBreaker;
    });

    test('implements CircuitBreakerInterface', function (): void {
        expect($this->circuitBreaker)->toBeInstanceOf(CircuitBreakerInterface::class);
    });

    test('should retry by default for new endpoints', function (): void {
        expect($this->circuitBreaker->shouldRetry('https://api.example.com/check'))->toBeTrue();
        expect($this->circuitBreaker->getFailureCount('https://api.example.com/check'))->toBe(0);
        expect($this->circuitBreaker->isOpen('https://api.example.com/check'))->toBeFalse();
    });

    test('tracks failure count correctly', function (): void {
        $endpoint = 'https://api.example.com/check';

        $this->circuitBreaker->recordFailure($endpoint);
        expect($this->circuitBreaker->getFailureCount($endpoint))->toBe(1);

        $this->circuitBreaker->recordFailure($endpoint);
        expect($this->circuitBreaker->getFailureCount($endpoint))->toBe(2);

        $this->circuitBreaker->recordFailure($endpoint);
        expect($this->circuitBreaker->getFailureCount($endpoint))->toBe(3);
    });

    test('allows retries below threshold', function (): void {
        $endpoint = 'https://api.example.com/check';

        // Record 4 failures (below threshold of 5)
        for ($i = 0; 4 > $i; ++$i) {
            $this->circuitBreaker->recordFailure($endpoint);
        }

        expect($this->circuitBreaker->getFailureCount($endpoint))->toBe(4);
        expect($this->circuitBreaker->shouldRetry($endpoint))->toBeTrue();
        expect($this->circuitBreaker->isOpen($endpoint))->toBeFalse();
    });

    test('opens circuit when threshold is reached', function (): void {
        $endpoint = 'https://api.example.com/check';

        // Record 5 failures (reaches threshold)
        for ($i = 0; 5 > $i; ++$i) {
            $this->circuitBreaker->recordFailure($endpoint);
        }

        expect($this->circuitBreaker->getFailureCount($endpoint))->toBe(5);
        expect($this->circuitBreaker->shouldRetry($endpoint))->toBeFalse();
        expect($this->circuitBreaker->isOpen($endpoint))->toBeTrue();
    });

    test('prevents retries when circuit is open', function (): void {
        $endpoint = 'https://api.example.com/check';

        // Open the circuit
        for ($i = 0; 6 > $i; ++$i) {
            $this->circuitBreaker->recordFailure($endpoint);
        }

        expect($this->circuitBreaker->shouldRetry($endpoint))->toBeFalse();
        expect($this->circuitBreaker->isOpen($endpoint))->toBeTrue();
    });

    test('resets on successful response', function (): void {
        $endpoint = 'https://api.example.com/check';

        // Record failures
        for ($i = 0; 3 > $i; ++$i) {
            $this->circuitBreaker->recordFailure($endpoint);
        }

        expect($this->circuitBreaker->getFailureCount($endpoint))->toBe(3);

        // Record success - should reset
        $this->circuitBreaker->recordSuccess($endpoint);

        expect($this->circuitBreaker->getFailureCount($endpoint))->toBe(0);
        expect($this->circuitBreaker->shouldRetry($endpoint))->toBeTrue();
        expect($this->circuitBreaker->isOpen($endpoint))->toBeFalse();
    });

    test('resets on successful response even when circuit was open', function (): void {
        $endpoint = 'https://api.example.com/check';

        // Open the circuit
        for ($i = 0; 5 > $i; ++$i) {
            $this->circuitBreaker->recordFailure($endpoint);
        }

        expect($this->circuitBreaker->isOpen($endpoint))->toBeTrue();

        // Record success - should reset completely
        $this->circuitBreaker->recordSuccess($endpoint);

        expect($this->circuitBreaker->getFailureCount($endpoint))->toBe(0);
        expect($this->circuitBreaker->shouldRetry($endpoint))->toBeTrue();
        expect($this->circuitBreaker->isOpen($endpoint))->toBeFalse();
    });

    test('handles multiple endpoints independently', function (): void {
        $endpoint1 = 'https://api.example.com/check';
        $endpoint2 = 'https://api.example.com/expand';

        // Fail endpoint1 but not endpoint2
        for ($i = 0; 5 > $i; ++$i) {
            $this->circuitBreaker->recordFailure($endpoint1);
        }

        $this->circuitBreaker->recordFailure($endpoint2);

        expect($this->circuitBreaker->isOpen($endpoint1))->toBeTrue();
        expect($this->circuitBreaker->isOpen($endpoint2))->toBeFalse();

        expect($this->circuitBreaker->getFailureCount($endpoint1))->toBe(5);
        expect($this->circuitBreaker->getFailureCount($endpoint2))->toBe(1);
    });

    test('normalizes endpoint keys correctly', function (): void {
        // Different query parameters should be treated as same endpoint
        $endpoint1 = 'https://api.example.com/check?store_id=123';
        $endpoint2 = 'https://api.example.com/check?store_id=456';

        $this->circuitBreaker->recordFailure($endpoint1);
        $this->circuitBreaker->recordFailure($endpoint2);

        // Should be treated as same endpoint (host + path)
        expect($this->circuitBreaker->getFailureCount($endpoint1))->toBe(2);
        expect($this->circuitBreaker->getFailureCount($endpoint2))->toBe(2);
    });

    test('handles invalid URLs gracefully', function (): void {
        $invalidEndpoint = 'not-a-valid-url';

        $this->circuitBreaker->recordFailure($invalidEndpoint);
        expect($this->circuitBreaker->getFailureCount($invalidEndpoint))->toBe(1);
        expect($this->circuitBreaker->shouldRetry($invalidEndpoint))->toBeTrue();
    });

    test('handles URLs with different components', function (): void {
        $baseUrl = 'https://api.example.com';
        $pathUrl = 'https://api.example.com/api/v1/check';
        $queryUrl = 'https://api.example.com/api/v1/check?param=value';
        $fragmentUrl = 'https://api.example.com/api/v1/check#section';

        $this->circuitBreaker->recordFailure($pathUrl);

        // Base URL should be different endpoint
        expect($this->circuitBreaker->getFailureCount($baseUrl))->toBe(0);

        // Query params should map to same endpoint
        expect($this->circuitBreaker->getFailureCount($queryUrl))->toBe(1);

        // Fragment should map to same endpoint
        expect($this->circuitBreaker->getFailureCount($fragmentUrl))->toBe(1);
    });

    test('correctly identifies different hosts as separate endpoints', function (): void {
        $endpoint1 = 'https://api1.example.com/check';
        $endpoint2 = 'https://api2.example.com/check';

        $this->circuitBreaker->recordFailure($endpoint1);

        expect($this->circuitBreaker->getFailureCount($endpoint1))->toBe(1);
        expect($this->circuitBreaker->getFailureCount($endpoint2))->toBe(0);
    });

    test('correctly identifies different paths as separate endpoints', function (): void {
        $endpoint1 = 'https://api.example.com/check';
        $endpoint2 = 'https://api.example.com/expand';

        $this->circuitBreaker->recordFailure($endpoint1);

        expect($this->circuitBreaker->getFailureCount($endpoint1))->toBe(1);
        expect($this->circuitBreaker->getFailureCount($endpoint2))->toBe(0);
    });

    test('handles edge case thresholds correctly', function (): void {
        $endpoint = 'https://api.example.com/check';

        // Test exactly at threshold
        for ($i = 0; 5 > $i; ++$i) {
            $this->circuitBreaker->recordFailure($endpoint);
        }

        expect($this->circuitBreaker->shouldRetry($endpoint))->toBeFalse();

        // Test one below threshold
        $this->circuitBreaker->recordSuccess($endpoint);

        for ($i = 0; 4 > $i; ++$i) {
            $this->circuitBreaker->recordFailure($endpoint);
        }

        expect($this->circuitBreaker->shouldRetry($endpoint))->toBeTrue();
    });
});
