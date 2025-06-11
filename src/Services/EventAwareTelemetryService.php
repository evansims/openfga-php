<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use OpenFGA\Events\{EventDispatcherInterface, OperationCompletedEvent, OperationStartedEvent};
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use OpenFGA\Observability\TelemetryInterface;
use Override;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

use function is_string;

/**
 * Event-aware telemetry service that emits domain events.
 *
 * This service extends the base TelemetryService functionality by emitting
 * domain events for operation lifecycle, enabling decoupled observability.
 */
final readonly class EventAwareTelemetryService implements TelemetryServiceInterface
{
    /**
     * Create a new event-aware telemetry service instance.
     *
     * @param TelemetryInterface|null       $telemetry       The underlying telemetry provider
     * @param EventDispatcherInterface|null $eventDispatcher Optional event dispatcher for domain events
     */
    public function __construct(
        private ?TelemetryInterface $telemetry,
        private ?EventDispatcherInterface $eventDispatcher = null,
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
        $this->telemetry?->recordAuthenticationEvent($event, $success, $duration, $attributes);
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
        $this->telemetry?->endOperation(
            $context->span,
            false,
            $exception,
            array_merge($context->attributes, [
                'result_type' => null !== $result ? get_debug_type($result) : null,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]),
        );

        // Record operation metrics with error flag
        $this->recordOperationMetrics(
            $context->operation,
            $duration,
            $context->store,
            $context->model,
            array_merge($context->attributes, [
                'error' => true,
                'error_type' => $exception::class,
            ]),
        );

        // Emit operation completed event
        if ($this->eventDispatcher instanceof EventDispatcherInterface) {
            $this->eventDispatcher->dispatch(new OperationCompletedEvent(
                operation: $context->operation,
                success: false,
                exception: $exception,
                storeId: $this->extractStoreId($context->store),
                modelId: $this->extractModelId($context->model),
                context: $context->attributes,
                result: $result,
            ));
        }
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
        $span = $this->telemetry?->startHttpRequest($request);

        // End the span immediately with the provided data
        $this->telemetry?->endHttpRequest($span, $response, $exception);
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
        $this->telemetry?->recordOperationMetrics($operation, $duration, $store, $model, $attributes);
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
        $this->telemetry?->endOperation(
            $context->span,
            true,
            null,
            array_merge($context->attributes, [
                'result_type' => null !== $result ? get_debug_type($result) : null,
            ]),
        );

        // Record operation metrics
        $this->recordOperationMetrics(
            $context->operation,
            $duration,
            $context->store,
            $context->model,
            $context->attributes,
        );

        // Emit operation completed event
        if ($this->eventDispatcher instanceof EventDispatcherInterface) {
            $this->eventDispatcher->dispatch(new OperationCompletedEvent(
                operation: $context->operation,
                success: true,
                storeId: $this->extractStoreId($context->store),
                modelId: $this->extractModelId($context->model),
                context: $context->attributes,
                result: $result,
            ));
        }
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
        $span = $this->telemetry?->startOperation($operation, $store, $model, $attributes);

        // Emit operation started event
        if ($this->eventDispatcher instanceof EventDispatcherInterface) {
            $this->eventDispatcher->dispatch(new OperationStartedEvent(
                operation: $operation,
                storeId: $this->extractStoreId($store),
                modelId: $this->extractModelId($model),
                context: $attributes,
            ));
        }

        return new TelemetryContext(
            operation: $operation,
            store: $store,
            model: $model,
            startTime: $startTime,
            span: $span,
            attributes: $attributes,
        );
    }

    /**
     * Extract model ID from model parameter.
     *
     * @param AuthorizationModelInterface|string|null $model
     */
    private function extractModelId(AuthorizationModelInterface | string | null $model): ?string
    {
        if ($model instanceof AuthorizationModelInterface) {
            return $model->getId();
        }

        return is_string($model) ? $model : null;
    }

    /**
     * Extract store ID from store parameter.
     *
     * @param StoreInterface|string $store
     */
    private function extractStoreId(StoreInterface | string $store): string
    {
        if ($store instanceof StoreInterface) {
            return $store->getId();
        }

        return $store;
    }
}
