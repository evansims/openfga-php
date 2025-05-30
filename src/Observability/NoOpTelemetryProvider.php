<?php

declare(strict_types=1);

namespace OpenFGA\Observability;

use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use Override;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

/**
 * No-operation telemetry provider for when OpenTelemetry is not available.
 *
 * This class provides a safe fallback implementation of the TelemetryInterface
 * that performs no operations. It ensures the OpenFGA SDK remains fully
 * functional even when OpenTelemetry dependencies are not installed or
 * configured, maintaining backward compatibility and optional observability.
 *
 * All methods in this class are designed to be as lightweight as possible,
 * introducing minimal overhead when telemetry is disabled. The class follows
 * the null object pattern to eliminate the need for conditional checks
 * throughout the SDK codebase.
 */
final class NoOpTelemetryProvider implements TelemetryInterface
{
    /**
     * @inheritDoc
     */
    #[Override]
    public function endHttpRequest(
        mixed $span,
        ?ResponseInterface $response = null,
        ?Throwable $exception = null,
    ): void {
        // No-op
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function endOperation(
        mixed $span,
        bool $success,
        ?Throwable $exception = null,
        array $attributes = [],
    ): void {
        // No-op
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function recordAuthenticationEvent(
        string $event,
        bool $success,
        float $duration,
        array $attributes = [],
    ): void {
        // No-op
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function recordCircuitBreakerState(
        string $endpoint,
        string $state,
        int $failures,
        float $failureRate,
    ): void {
        // No-op
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function recordOperationMetrics(
        string $operation,
        float $duration,
        StoreInterface | string $store,
        AuthorizationModelInterface | string | null $model = null,
        array $attributes = [],
    ): void {
        // No-op
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function recordRetryAttempt(
        string $endpoint,
        int $attempt,
        int $delayMs,
        string $outcome,
        ?Throwable $exception = null,
    ): void {
        // No-op
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function startHttpRequest(RequestInterface $request): null
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function startOperation(
        string $operation,
        StoreInterface | string $store,
        AuthorizationModelInterface | string | null $model = null,
        array $attributes = [],
    ): null {
        return null;
    }
}
