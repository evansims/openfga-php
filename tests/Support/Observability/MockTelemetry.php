<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Observability;

use OpenFGA\Observability\TelemetryInterface;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

/**
 * Mock telemetry implementation for testing observability functionality.
 */
final class MockTelemetry implements TelemetryInterface
{
    public array $recordedSpans = [];

    public function endHttpRequest(object | null $span, ?ResponseInterface $response = null, ?Throwable $exception = null): void
    {
    }

    public function endOperation(object | null $span, bool $success, ?Throwable $exception = null, array $attributes = []): void
    {
    }

    public function recordAuthenticationEvent(string $event, bool $success, float $duration, array $attributes = []): void
    {
    }

    public function recordCircuitBreakerState(string $endpoint, string $state, int $failures, float $failureRate): void
    {
    }

    public function recordOperationMetrics(string $operation, float $duration, $store, $model = null, array $attributes = []): void
    {
    }

    public function recordRetryAttempt(string $endpoint, int $attempt, int $delayMs, string $outcome, ?Throwable $exception = null): void
    {
    }

    public function recordSpan(string $name, array $attributes = []): void
    {
        $this->recordedSpans[] = ['name' => $name, 'attributes' => $attributes];
    }

    public function startHttpRequest(RequestInterface $request): object | null
    {
        return null;
    }

    public function startOperation(string $operation, $store, $model = null, array $attributes = []): object | null
    {
        return null;
    }
}
