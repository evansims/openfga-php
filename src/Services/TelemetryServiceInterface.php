<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

/**
 * Service interface for managing telemetry and observability in OpenFGA operations.
 *
 * This service provides a higher-level abstraction over the telemetry infrastructure,
 * handling the creation, management, and coordination of telemetry spans, metrics,
 * and events. It simplifies telemetry usage by providing business-focused methods
 * that handle common patterns like operation timing and error tracking.
 *
 * ## Core Functionality
 *
 * The service manages the lifecycle of telemetry data for:
 * - HTTP requests and responses with automatic span management
 * - Business operations with timing and success/failure tracking
 * - Error and exception handling with contextual information
 * - Performance metrics and operational insights
 *
 * ## Usage Example
 *
 * ```php
 * $telemetryService = new TelemetryService($telemetryProvider);
 *
 * // Track a complete operation
 * $context = $telemetryService->startOperation('check', $store, $model);
 * try {
 *     $result = $businessLogic();
 *     $telemetryService->recordSuccess($context, $result);
 *     return $result;
 * } catch (Throwable $error) {
 *     $telemetryService->recordFailure($context, $error);
 *     throw $error;
 * }
 * ```
 *
 * @see TelemetryInterface Lower-level telemetry provider
 */
interface TelemetryServiceInterface
{
    /**
     * Record an authentication event with duration and outcome.
     *
     * Tracks authentication-related operations including token acquisition,
     * renewal, and validation. Provides insights into authentication
     * performance and failure patterns.
     *
     * @param string               $event      The authentication event type
     * @param bool                 $success    Whether the event was successful
     * @param float                $duration   Event duration in seconds
     * @param array<string, mixed> $attributes Additional event context
     */
    public function recordAuthenticationEvent(
        string $event,
        bool $success,
        float $duration,
        array $attributes = [],
    ): void;

    /**
     * Record a failed operation with error details.
     *
     * Completes an operation context with failure information, including
     * exception details and any additional error context. This provides
     * structured error tracking for debugging and monitoring.
     *
     * @param TelemetryContext $context   The operation context from startOperation()
     * @param Throwable        $exception The exception that caused the failure
     * @param mixed            $result    Optional partial result data
     */
    public function recordFailure(
        TelemetryContext $context,
        Throwable $exception,
        mixed $result = null,
    ): void;

    /**
     * Record an HTTP request/response pair with automatic span management.
     *
     * Handles the complete lifecycle of HTTP request telemetry, including
     * span creation, timing, and completion with response or error details.
     * Ideal for tracking individual API calls.
     *
     * @param RequestInterface       $request   The HTTP request being tracked
     * @param ResponseInterface|null $response  The HTTP response received
     * @param Throwable|null         $exception Optional exception that occurred
     * @param float|null             $duration  Optional manual duration override
     */
    public function recordHttpRequest(
        RequestInterface $request,
        ?ResponseInterface $response = null,
        ?Throwable $exception = null,
        ?float $duration = null,
    ): void;

    /**
     * Record operational metrics for performance monitoring.
     *
     * Tracks operation-level metrics including timing, throughput, and
     * contextual information about stores and models. Used for performance
     * analysis and capacity planning.
     *
     * @param string                                  $operation  The operation name
     * @param float                                   $duration   Operation duration in seconds
     * @param StoreInterface|string                   $store      The store context
     * @param AuthorizationModelInterface|string|null $model      Optional model context
     * @param array<string, mixed>                    $attributes Additional metrics context
     */
    public function recordOperationMetrics(
        string $operation,
        float $duration,
        StoreInterface | string $store,
        AuthorizationModelInterface | string | null $model = null,
        array $attributes = [],
    ): void;

    /**
     * Record a successful operation with results.
     *
     * Completes an operation context with success information and any
     * relevant result data. This tracks successful operation patterns
     * and performance characteristics.
     *
     * @param TelemetryContext $context The operation context from startOperation()
     * @param mixed            $result  The operation result data
     */
    public function recordSuccess(
        TelemetryContext $context,
        mixed $result = null,
    ): void;

    /**
     * Start tracking a business operation.
     *
     * Creates a new telemetry context for tracking a complete business operation
     * including timing, success/failure status, and contextual information.
     * Returns a context object that should be passed to recordSuccess/recordFailure.
     *
     * @param  string                                  $operation  The operation name
     * @param  StoreInterface|string                   $store      The store context
     * @param  AuthorizationModelInterface|string|null $model      Optional model context
     * @param  array<string, mixed>                    $attributes Additional operation context
     * @return TelemetryContext                        Context for completing the operation tracking
     */
    public function startOperation(
        string $operation,
        StoreInterface | string $store,
        AuthorizationModelInterface | string | null $model = null,
        array $attributes = [],
    ): TelemetryContext;
}

/**
 * Context object for tracking ongoing telemetry operations.
 *
 * Encapsulates the state needed to complete telemetry tracking for an operation,
 * including span information, timing data, and contextual metadata.
 */
final readonly class TelemetryContext
{
    /**
     * @param array<string, mixed>                    $attributes
     * @param string                                  $operation
     * @param StoreInterface|string                   $store
     * @param AuthorizationModelInterface|string|null $model
     * @param float                                   $startTime
     * @param object|null                             $span
     */
    public function __construct(
        public string $operation,
        public StoreInterface | string $store,
        public AuthorizationModelInterface | string | null $model,
        public float $startTime,
        public object | null $span,
        public array $attributes = [],
    ) {
    }

    /**
     * Calculate the duration since operation start.
     *
     * @return float Duration in seconds
     */
    public function getDuration(): float
    {
        return microtime(true) - $this->startTime;
    }

    /**
     * Get the model ID regardless of whether model is an object or string.
     *
     * @return string|null The model identifier
     */
    public function getModelId(): ?string
    {
        if (null === $this->model) {
            return null;
        }

        return $this->model instanceof AuthorizationModelInterface
            ? $this->model->getId()
            : $this->model;
    }

    /**
     * Get the store ID regardless of whether store is an object or string.
     *
     * @return string The store identifier
     */
    public function getStoreId(): string
    {
        return $this->store instanceof StoreInterface
            ? $this->store->getId()
            : $this->store;
    }
}
