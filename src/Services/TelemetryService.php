<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use OpenFGA\Observability\TelemetryInterface;
use Override;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

/**
 * Service implementation for managing telemetry and observability in OpenFGA operations.
 *
 * Provides a higher-level abstraction over the telemetry infrastructure, simplifying
 * the creation and management of telemetry spans, metrics, and events. This service
 * handles common telemetry patterns and provides business-focused methods for
 * tracking operations, performance, and errors.
 *
 * @see TelemetryServiceInterface Service interface
 * @see TelemetryInterface Lower-level telemetry provider
 */
final readonly class TelemetryService implements TelemetryServiceInterface
{
    /**
     * Create a new telemetry service instance.
     *
     * @param TelemetryInterface $telemetry The underlying telemetry provider
     */
    public function __construct(
        private TelemetryInterface $telemetry,
    ) {
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
        $this->telemetry->recordAuthenticationEvent($event, $success, $duration, $attributes);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function recordFailure(
        TelemetryContext $context,
        Throwable $exception,
        mixed $result = null,
    ): void {
        $duration = $context->getDuration();

        // End the operation span with failure details
        /** @var array<string, mixed> $attributes */
        $attributes = array_merge($context->attributes, [
            'result_type' => null !== $result ? get_debug_type($result) : null,
            'error_class' => $exception::class,
            'error_message' => $exception->getMessage(),
        ]);

        $this->telemetry->endOperation(
            $context->span,
            false,
            $exception,
            $attributes,
        );

        // Record operation metrics with error flag
        /** @var array<string, mixed> $errorAttributes */
        $errorAttributes = array_merge($context->attributes, [
            'error' => true,
            'error_type' => $exception::class,
        ]);

        $this->recordOperationMetrics(
            $context->operation,
            $duration,
            $context->store,
            $context->model,
            $errorAttributes,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function recordHttpRequest(
        RequestInterface $request,
        ?ResponseInterface $response = null,
        ?Throwable $exception = null,
        ?float $duration = null,
    ): void {
        // For standalone HTTP request tracking, create a minimal span
        $span = $this->telemetry->startHttpRequest($request);

        // End the span immediately with the provided data
        $this->telemetry->endHttpRequest($span, $response, $exception);
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
        $this->telemetry->recordOperationMetrics($operation, $duration, $store, $model, $attributes);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function recordSuccess(
        TelemetryContext $context,
        mixed $result = null,
    ): void {
        $duration = $context->getDuration();

        // End the operation span with success details
        /** @var array<string, mixed> $successAttributes */
        $successAttributes = array_merge($context->attributes, [
            'result_type' => null !== $result ? get_debug_type($result) : null,
        ]);

        $this->telemetry->endOperation(
            $context->span,
            true,
            null,
            $successAttributes,
        );

        // Record operation metrics
        $this->recordOperationMetrics(
            $context->operation,
            $duration,
            $context->store,
            $context->model,
            $context->attributes,
        );
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
    ): TelemetryContext {
        $startTime = microtime(true);
        $span = $this->telemetry->startOperation($operation, $store, $model, $attributes);

        return new TelemetryContext(
            operation: $operation,
            store: $store,
            model: $model,
            startTime: $startTime,
            span: $span,
            attributes: $attributes,
        );
    }
}
