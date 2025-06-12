<?php

declare(strict_types=1);

namespace OpenFGA\Observability;

use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

/**
 * Interface for OpenTelemetry integration in the OpenFGA SDK.
 *
 * This interface provides methods for instrumenting OpenFGA operations with
 * observability features including distributed tracing, metrics collection,
 * and structured logging. Implementations should integrate with OpenTelemetry
 * or other observability platforms to provide insights into SDK performance
 * and operation outcomes.
 *
 * The interface supports both automatic instrumentation of HTTP requests and
 * business-level instrumentation of OpenFGA API operations. All methods are
 * designed to be safe to call even when no telemetry backend is configured,
 * ensuring the SDK remains functional without observability dependencies.
 *
 * @see https://opentelemetry.io/docs/concepts/signals/ OpenTelemetry signals overview
 * @see https://opentelemetry.io/docs/specs/semconv/http/ HTTP semantic conventions
 */
interface TelemetryInterface
{
    /**
     * End tracing for an HTTP request.
     *
     * Completes the HTTP request span, recording the response status and any
     * errors that occurred. The span should include standard HTTP response
     * attributes such as status code and response size.
     *
     * @param object|null            $span      The span identifier returned by startHttpRequest()
     * @param ResponseInterface|null $response  The HTTP response received, if any
     * @param Throwable|null         $exception Optional exception that occurred during the request
     */
    public function endHttpRequest(
        object | null $span,
        ?ResponseInterface $response = null,
        ?Throwable $exception = null,
    ): void;

    /**
     * End tracing for an OpenFGA API operation.
     *
     * Completes the trace span started with startOperation(), recording the
     * operation outcome and any relevant metrics. If an exception occurred
     * during the operation, it should be recorded in the span.
     *
     * @param object|null          $span       The span identifier returned by startOperation()
     * @param bool                 $success    Whether the operation completed successfully
     * @param Throwable|null       $exception  Optional exception that occurred during the operation
     * @param array<string, mixed> $attributes Additional attributes to record
     */
    public function endOperation(
        object | null $span,
        bool $success,
        ?Throwable $exception = null,
        array $attributes = [],
    ): void;

    /**
     * Record authentication events.
     *
     * Records metrics and traces related to authentication flows, including
     * token acquisition, refresh operations, and authentication failures.
     * This helps monitor authentication performance and troubleshoot auth issues.
     *
     * @param string               $event      The authentication event type ('token_request', 'token_refresh', 'auth_failure')
     * @param bool                 $success    Whether the authentication event was successful
     * @param float                $duration   The duration of the authentication operation in seconds
     * @param array<string, mixed> $attributes Additional event attributes
     */
    public function recordAuthenticationEvent(
        string $event,
        bool $success,
        float $duration,
        array $attributes = [],
    ): void;

    /**
     * Record circuit breaker state changes.
     *
     * Records metrics about circuit breaker state transitions and failure rates.
     * This helps monitor the health of individual API endpoints and the SDK's
     * resilience mechanisms.
     *
     * @param string $endpoint    The API endpoint this circuit breaker protects
     * @param string $state       The new circuit breaker state ('open', 'closed', 'half_open')
     * @param int    $failures    The current failure count
     * @param float  $failureRate The current failure rate (0.0 to 1.0)
     */
    public function recordCircuitBreakerState(
        string $endpoint,
        string $state,
        int $failures,
        float $failureRate,
    ): void;

    /**
     * Record performance metrics for OpenFGA operations.
     *
     * Records timing and throughput metrics for OpenFGA API operations,
     * allowing monitoring of operation latency and identifying performance
     * bottlenecks or degradations.
     *
     * @param string                                  $operation  The OpenFGA operation name
     * @param float                                   $duration   The operation duration in seconds
     * @param StoreInterface|string                   $store      The store being operated on
     * @param AuthorizationModelInterface|string|null $model      The authorization model used
     * @param array<string, mixed>                    $attributes Additional metric attributes
     */
    public function recordOperationMetrics(
        string $operation,
        float $duration,
        StoreInterface | string $store,
        AuthorizationModelInterface | string | null $model = null,
        array $attributes = [],
    ): void;

    /**
     * Record retry attempt metrics.
     *
     * Records metrics about retry attempts, including the retry count, delay,
     * and eventual outcome. This helps track the reliability and performance
     * of API requests under various network conditions.
     *
     * @param string         $endpoint  The API endpoint being retried
     * @param int            $attempt   The current attempt number (1-based)
     * @param int            $delayMs   The delay before this attempt in milliseconds
     * @param string         $outcome   The outcome of this attempt ('success', 'failure', 'retry')
     * @param Throwable|null $exception Optional exception from this attempt
     */
    public function recordRetryAttempt(
        string $endpoint,
        int $attempt,
        int $delayMs,
        string $outcome,
        ?Throwable $exception = null,
    ): void;

    /**
     * Record a telemetry span with attributes.
     *
     * Records a complete telemetry span for events that don't require
     * start/end semantics. This is useful for event-driven telemetry
     * where the event represents a point in time rather than a duration.
     *
     * @param string               $name       The span name
     * @param array<string, mixed> $attributes Span attributes
     */
    public function recordSpan(string $name, array $attributes = []): void;

    /**
     * Start tracing an HTTP request.
     *
     * Creates a new trace span for an outgoing HTTP request to the OpenFGA API.
     * The span should follow OpenTelemetry semantic conventions for HTTP client
     * operations, including standard HTTP attributes.
     *
     * @param  RequestInterface $request The HTTP request being sent
     * @return object|null      A span identifier or context that can be passed to endHttpRequest()
     */
    public function startHttpRequest(RequestInterface $request): object | null;

    /**
     * Start tracing an OpenFGA API operation.
     *
     * Creates a new trace span for a high-level OpenFGA operation such as check,
     * expand, or write operations. The span should include relevant attributes
     * such as store ID, authorization model ID, and operation-specific metadata.
     *
     * @param  string                                  $operation  The OpenFGA operation name (for example 'check', 'expand', 'write_tuples')
     * @param  StoreInterface|string                   $store      The store being operated on
     * @param  AuthorizationModelInterface|string|null $model      The authorization model being used
     * @param  array<string, mixed>                    $attributes Additional span attributes
     * @return object|null                             A span identifier or context that can be passed to endOperation()
     */
    public function startOperation(
        string $operation,
        StoreInterface | string $store,
        AuthorizationModelInterface | string | null $model = null,
        array $attributes = [],
    ): object | null;
}
