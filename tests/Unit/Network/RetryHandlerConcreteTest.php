<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Network;

use OpenFGA\Network\{CircuitBreakerInterface, RetryHandler};
use ReflectionClass;

describe('RetryHandler (Concrete Class)', function (): void {
    test('uses actual sleep implementation', function (): void {
        $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
        $handler = new RetryHandler($circuitBreaker);

        expect($handler)->toBeInstanceOf(RetryHandler::class);
    });

    test('sleep method performs actual delay', function (): void {
        $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
        $handler = new RetryHandler($circuitBreaker);

        // Use reflection to access the protected sleep method
        $reflection = new ReflectionClass($handler);
        $sleepMethod = $reflection->getMethod('sleep');
        $sleepMethod->setAccessible(true);

        $startTime = microtime(true);
        $sleepMethod->invoke($handler, 10); // 10ms delay
        $endTime = microtime(true);

        // Should have delayed at least 8ms (allowing for timing variance)
        expect(($endTime - $startTime) * 1000)->toBeGreaterThan(8);
    });

    test('sleep method handles zero delay', function (): void {
        $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
        $handler = new RetryHandler($circuitBreaker);

        $reflection = new ReflectionClass($handler);
        $sleepMethod = $reflection->getMethod('sleep');
        $sleepMethod->setAccessible(true);

        $startTime = microtime(true);
        $sleepMethod->invoke($handler, 0); // No delay
        $endTime = microtime(true);

        // Should complete almost immediately
        expect(($endTime - $startTime) * 1000)->toBeLessThan(1);
    });

    test('sleep method handles negative delay', function (): void {
        $circuitBreaker = test()->createMock(CircuitBreakerInterface::class);
        $handler = new RetryHandler($circuitBreaker);

        $reflection = new ReflectionClass($handler);
        $sleepMethod = $reflection->getMethod('sleep');
        $sleepMethod->setAccessible(true);

        $startTime = microtime(true);
        $sleepMethod->invoke($handler, -5); // Negative delay
        $endTime = microtime(true);

        // Should complete almost immediately without error
        expect(($endTime - $startTime) * 1000)->toBeLessThan(1);
    });
});
