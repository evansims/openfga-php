<?php

declare(strict_types=1);

namespace OpenFGA\Observability;

use OpenFGA\Client;
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use OpenTelemetry\API\Common\Attribute\Attributes;
use Override;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

use function is_array;
use function is_int;
use function is_object;
use function is_string;

/**
 * OpenTelemetry implementation for OpenFGA SDK observability.
 *
 * This class provides comprehensive telemetry capabilities for the OpenFGA SDK
 * using OpenTelemetry APIs. It creates structured traces for all operations,
 * records performance metrics, and tracks reliability indicators such as retry
 * attempts and circuit breaker state changes.
 *
 * The implementation follows OpenTelemetry semantic conventions for HTTP clients
 * and RPC operations, ensuring compatibility with standard observability tools
 * and platforms. All telemetry is optional and gracefully degrades when
 * OpenTelemetry is not configured.
 *
 * @see https://opentelemetry.io/docs/specs/semconv/http/ HTTP semantic conventions
 * @see https://opentelemetry.io/docs/specs/semconv/rpc/ RPC semantic conventions
 */
final readonly class OpenTelemetryProvider implements TelemetryInterface
{
    /**
     * @var object Counter for authentication events
     */
    private object $authCounter;

    /**
     * @var object Histogram for authentication duration
     */
    private object $authDuration;

    /**
     * @var object Counter for circuit breaker state changes
     */
    private object $circuitBreakerCounter;

    /**
     * @var object Counter for HTTP requests
     */
    private object $httpRequestCounter;

    /**
     * @var object Counter for OpenFGA operations
     */
    private object $operationCounter;

    /**
     * @var object Histogram for OpenFGA operation duration
     */
    private object $operationDuration;

    /**
     * @var object Counter for retry attempts
     */
    private object $retryCounter;

    /**
     * @var object Histogram for retry delays
     */
    private object $retryDelay;

    /**
     * Create a new OpenTelemetry provider for OpenFGA SDK observability.
     *
     * @param object $tracer The OpenTelemetry tracer for creating spans
     * @param object $meter  The OpenTelemetry meter for recording metrics
     */
    public function __construct(private object $tracer, private object $meter)
    {
        // Initialize operation metrics
        $this->operationCounter = $this->createCounter(
            $this->meter,
            'openfga.operations.total',
            'operations',
            'Total number of OpenFGA API operations',
        );

        $this->operationDuration = $this->createHistogram(
            $this->meter,
            'openfga.operations.duration',
            'seconds',
            'Duration of OpenFGA API operations',
        );

        // Initialize HTTP metrics
        $this->httpRequestCounter = $this->createCounter(
            $this->meter,
            'openfga.http.requests.total',
            'requests',
            'Total number of HTTP requests to OpenFGA API',
        );

        // Initialize retry metrics
        $this->retryCounter = $this->createCounter(
            $this->meter,
            'openfga.retries.total',
            'attempts',
            'Total number of retry attempts',
        );

        $this->retryDelay = $this->createHistogram(
            $this->meter,
            'openfga.retries.delay',
            'milliseconds',
            'Delay before retry attempts',
        );

        // Initialize circuit breaker metrics
        $this->circuitBreakerCounter = $this->createCounter(
            $this->meter,
            'openfga.circuit_breaker.state_changes.total',
            'changes',
            'Total number of circuit breaker state changes',
        );

        // Initialize authentication metrics
        $this->authCounter = $this->createCounter(
            $this->meter,
            'openfga.auth.events.total',
            'events',
            'Total number of authentication events',
        );

        $this->authDuration = $this->createHistogram(
            $this->meter,
            'openfga.auth.duration',
            'seconds',
            'Duration of authentication operations',
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function endHttpRequest(
        mixed $span,
        ?ResponseInterface $response = null,
        ?Throwable $exception = null,
    ): void {
        if (! is_object($span)) {
            return;
        }

        // Record response details
        if ($response instanceof ResponseInterface) {
            $statusCode = $response->getStatusCode();

            if (method_exists($span, 'setAttribute')) {
                $span->setAttribute('http.status_code', $statusCode);
                $contentLength = $response->getHeaderLine('Content-Length');
                $span->setAttribute('http.response.size', '' !== $contentLength ? (int) $contentLength : 0);
            }

            // Set span status based on HTTP status code
            if (method_exists($span, 'setStatus')) {
                if (400 <= $statusCode) {
                    $span->setStatus('ERROR', 'HTTP ' . $statusCode);
                } else {
                    $span->setStatus('OK');
                }
            }
        }

        // Record exception if present
        if ($exception instanceof Throwable) {
            if (method_exists($span, 'recordException')) {
                $span->recordException($exception);
            }

            if (method_exists($span, 'setStatus')) {
                $span->setStatus('ERROR', $exception->getMessage()); // STATUS_ERROR
            }
        }

        // Extract details for metrics
        $method = 'unknown';
        $statusCode = 0;

        if (method_exists($span, 'getAttribute')) {
            /** @var mixed $methodAttribute */
            $methodAttribute = $span->getAttribute('http.method');

            /** @var mixed $statusCodeAttribute */
            $statusCodeAttribute = $span->getAttribute('http.status_code');
            $method = is_string($methodAttribute) ? $methodAttribute : 'unknown';
            $statusCode = is_int($statusCodeAttribute) ? $statusCodeAttribute : 0;
        }
        $success = ! ($exception instanceof Throwable) && (0 === $statusCode || 400 > $statusCode);

        /** @var mixed $metricAttributes */
        $metricAttributes = $this->createAttributes([
            'method' => $method,
            'status_code' => (string) $statusCode,
            'success' => $success ? 'true' : 'false',
        ]);

        // Record HTTP request counter
        $this->safeCounterAdd($this->httpRequestCounter, 1, $metricAttributes);

        if (method_exists($span, 'end')) {
            $span->end();
        }
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
        if (! is_object($span)) {
            return;
        }

        // Set additional attributes
        if (method_exists($span, 'setAttribute')) {
            /** @var mixed $value */
            foreach ($attributes as $key => $value) {
                $span->setAttribute($key, $value);
            }
        }

        // Record exception if present
        if ($exception instanceof Throwable) {
            if (method_exists($span, 'recordException')) {
                $span->recordException($exception);
            }

            if (method_exists($span, 'setStatus')) {
                $span->setStatus('ERROR', $exception->getMessage()); // STATUS_ERROR
            }
        } elseif (method_exists($span, 'setStatus')) {
            $span->setStatus($success ? 'OK' : 'ERROR');
            // STATUS_OK : STATUS_ERROR
        }

        // Extract operation details for metrics
        $operation = 'unknown';
        $storeId = 'unknown';
        $modelId = null;

        if (method_exists($span, 'getAttribute')) {
            /** @var mixed $operationAttribute */
            $operationAttribute = $span->getAttribute('openfga.operation');

            /** @var mixed $storeIdAttribute */
            $storeIdAttribute = $span->getAttribute('openfga.store_id');
            $operation = is_string($operationAttribute) ? $operationAttribute : 'unknown';
            $storeId = is_string($storeIdAttribute) ? $storeIdAttribute : 'unknown';

            /** @var mixed $modelId */
            $modelId = $span->getAttribute('openfga.model_id');
        }

        /** @var mixed $metricAttributes */
        $metricAttributes = $this->createAttributes([
            'operation' => $operation,
            'store_id' => $storeId,
            'success' => $success ? 'true' : 'false',
        ]);

        if (null !== $modelId && is_string($modelId)) {
            /** @var mixed $metricAttributes */
            $metricAttributes = $this->addAttribute($metricAttributes, 'model_id', $modelId);
        }

        // Record operation counter
        $this->safeCounterAdd($this->operationCounter, 1, $metricAttributes);

        if (method_exists($span, 'end')) {
            $span->end();
        }
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
        /** @var mixed $metricAttributes */
        $metricAttributes = $this->createAttributes(array_merge([
            'event' => $event,
            'success' => $success ? 'true' : 'false',
        ], $attributes));

        // Record authentication event counter
        $this->safeCounterAdd($this->authCounter, 1, $metricAttributes);

        // Record authentication duration
        $this->safeHistogramRecord($this->authDuration, $duration, $metricAttributes);

        // Create a span for the authentication event
        $span = $this->createSpan($this->tracer, 'openfga.auth.' . $event, 1); // KIND_INTERNAL

        if (method_exists($span, 'setAttributes')) {
            $span->setAttributes($this->addAttribute($metricAttributes, 'auth.duration', $duration));
        }

        if (method_exists($span, 'setStatus')) {
            $span->setStatus($success ? 'OK' : 'ERROR'); // STATUS_OK : STATUS_ERROR
        }

        if (method_exists($span, 'end')) {
            $span->end();
        }
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
        /** @var mixed $attributes */
        $attributes = $this->createAttributes([
            'endpoint' => $endpoint,
            'state' => $state,
            'failures' => $failures,
            'failure_rate' => $failureRate,
        ]);

        // Record circuit breaker state change
        $this->safeCounterAdd($this->circuitBreakerCounter, 1, $attributes);

        // Create a span for the state change
        $span = $this->createSpan($this->tracer, 'openfga.circuit_breaker_state_change', 1); // KIND_INTERNAL

        if (method_exists($span, 'setAttributes')) {
            $span->setAttributes($attributes);
        }

        if (method_exists($span, 'addEvent')) {
            $span->addEvent('circuit_breaker_state_changed', $attributes);
        }

        if (method_exists($span, 'setStatus')) {
            $span->setStatus('OK'); // STATUS_OK
        }

        if (method_exists($span, 'end')) {
            $span->end();
        }
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
        $storeId = $store instanceof StoreInterface ? $store->getId() : $store;
        $modelId = null;

        if ($model instanceof AuthorizationModelInterface) {
            $modelId = $model->getId();
        } elseif (is_string($model)) {
            $modelId = $model;
        }

        /** @var mixed $metricAttributes */
        $metricAttributes = $this->createAttributes(array_merge([
            'operation' => $operation,
            'store_id' => $storeId,
        ], $attributes));

        if (null !== $modelId) {
            /** @var mixed $metricAttributes */
            $metricAttributes = $this->addAttribute($metricAttributes, 'model_id', $modelId);
        }

        // Record operation duration
        $this->safeHistogramRecord($this->operationDuration, $duration, $metricAttributes);
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
        /** @var mixed $attributes */
        $attributes = $this->createAttributes([
            'endpoint' => $endpoint,
            'attempt' => $attempt,
            'outcome' => $outcome,
        ]);

        // Record retry attempt counter
        $this->safeCounterAdd($this->retryCounter, 1, $attributes);

        // Record retry delay histogram
        $this->safeHistogramRecord($this->retryDelay, $delayMs, $attributes);

        // Create a span for this retry attempt
        $span = $this->createSpan($this->tracer, 'openfga.retry_attempt', 1); // KIND_INTERNAL

        if (method_exists($span, 'setAttributes')) {
            $span->setAttributes($this->addAttribute($attributes, 'retry.delay_ms', $delayMs));
        }

        if ($exception instanceof Throwable) {
            if (method_exists($span, 'recordException')) {
                $span->recordException($exception);
            }

            if (method_exists($span, 'setStatus')) {
                $span->setStatus('ERROR', $exception->getMessage()); // STATUS_ERROR
            }
        } elseif (method_exists($span, 'setStatus')) {
            $span->setStatus('success' === $outcome ? 'OK' : 'ERROR');
            // STATUS_OK : STATUS_ERROR
        }

        if (method_exists($span, 'end')) {
            $span->end();
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function startHttpRequest(RequestInterface $request): object
    {
        $method = $request->getMethod();
        $uri = $request->getUri();
        $url = $uri->__toString();

        /** @var mixed $spanAttributes */
        $spanAttributes = $this->createAttributes([
            'http.method' => $method,
            'http.url' => $url,
            'http.scheme' => $uri->getScheme(),
            'http.host' => $uri->getHost(),
            'http.target' => $uri->getPath() . ('' !== $uri->getQuery() ? '?' . $uri->getQuery() : ''),
            'http.user_agent' => $request->getHeaderLine('User-Agent'),
            'openfga.sdk.name' => 'openfga-php',
            'openfga.sdk.version' => Client::VERSION,
        ]);

        $port = $uri->getPort();

        if (null !== $port) {
            /** @var mixed $spanAttributes */
            $spanAttributes = $this->addAttribute($spanAttributes, 'net.peer.port', $port);
        }

        $span = $this->createSpan($this->tracer, 'HTTP ' . $method, 3); // KIND_CLIENT

        if (method_exists($span, 'setAttributes')) {
            $span->setAttributes($spanAttributes);
        }

        return $span;
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
    ): object {
        $storeId = $store instanceof StoreInterface ? $store->getId() : $store;
        $modelId = null;

        if ($model instanceof AuthorizationModelInterface) {
            $modelId = $model->getId();
        } elseif (is_string($model)) {
            $modelId = $model;
        }

        /** @var mixed $spanAttributes */
        $spanAttributes = $this->createAttributes(array_merge([
            'openfga.operation' => $operation,
            'openfga.store_id' => $storeId,
            'openfga.sdk.name' => 'openfga-php',
            'openfga.sdk.version' => Client::VERSION,
        ], $attributes));

        if (null !== $modelId) {
            /** @var mixed $spanAttributes */
            $spanAttributes = $this->addAttribute($spanAttributes, 'openfga.model_id', $modelId);
        }

        $span = $this->createSpan($this->tracer, 'openfga.' . $operation, 3); // KIND_CLIENT

        if (method_exists($span, 'setAttributes')) {
            $span->setAttributes($spanAttributes);
        }

        return $span;
    }

    /**
     * Add an attribute to an existing attributes object.
     *
     * @param mixed  $attributes
     * @param string $key
     * @param mixed  $value
     */
    private function addAttribute(mixed $attributes, string $key, mixed $value): mixed
    {
        if (is_object($attributes) && method_exists($attributes, 'with')) {
            return $attributes->with($key, $value);
        }

        if (is_array($attributes)) {
            /** @var array<string, mixed> $arrayAttributes */
            $arrayAttributes = $attributes;
            // Add mixed telemetry attribute value to array
            $this->assignMixed($arrayAttributes, $key, $value);

            return $arrayAttributes;
        }

        return $attributes;
    }

    /**
     * Safely assign a mixed value to an array to satisfy Psalm.
     *
     * @param array<string, mixed> $array The target array
     * @param string               $key   The array key
     * @param mixed                $value The value to assign
     *
     * @psalm-suppress MixedAssignment
     */
    private function assignMixed(array &$array, string $key, mixed $value): void
    {
        $array[$key] = $value;
    }

    /**
     * Create OpenTelemetry attributes from an array.
     *
     * @param array<string, mixed> $attributes
     */
    private function createAttributes(array $attributes): mixed
    {
        if (class_exists('OpenTelemetry\API\Common\Attribute\Attributes')) {
            return Attributes::create($attributes);
        }

        return $attributes;
    }

    /**
     * Safely create a counter from a meter object.
     *
     * @param object $meter
     * @param string $name
     * @param string $unit
     * @param string $description
     */
    private function createCounter(object $meter, string $name, string $unit, string $description): object
    {
        if (method_exists($meter, 'createCounter')) {
            /** @var mixed $counter */
            $counter = $meter->createCounter($name, $unit, $description);

            if (is_object($counter)) {
                return $counter;
            }
        }

        // Return a no-op object that safely handles method calls
        return new class {
            public function add(int $amount, mixed $attributes = null): void
            {
            }
        };
    }

    /**
     * Safely create a histogram from a meter object.
     *
     * @param object $meter
     * @param string $name
     * @param string $unit
     * @param string $description
     */
    private function createHistogram(object $meter, string $name, string $unit, string $description): object
    {
        if (method_exists($meter, 'createHistogram')) {
            /** @var mixed $histogram */
            $histogram = $meter->createHistogram($name, $unit, $description);

            if (is_object($histogram)) {
                return $histogram;
            }
        }

        // Return a no-op object that safely handles method calls
        return new class {
            public function record(float $amount, mixed $attributes = null): void
            {
            }
        };
    }

    /**
     * Safely create a span from a tracer object.
     *
     * @param object $tracer
     * @param string $name
     * @param int    $spanKind
     */
    private function createSpan(object $tracer, string $name, int $spanKind = 1): object
    {
        if (method_exists($tracer, 'spanBuilder')) {
            /** @var mixed $spanBuilder */
            $spanBuilder = $tracer->spanBuilder($name);

            if (is_object($spanBuilder) && method_exists($spanBuilder, 'setSpanKind')) {
                /** @var mixed $spanBuilder */
                $spanBuilder = $spanBuilder->setSpanKind($spanKind);
            }

            if (is_object($spanBuilder) && method_exists($spanBuilder, 'startSpan')) {
                /** @var mixed $span */
                $span = $spanBuilder->startSpan();

                if (is_object($span)) {
                    return $span;
                }
            }
        }

        // Return a no-op span object
        return new class {
            public function setAttribute(string $key, mixed $value): static
            {
                return $this;
            }

            public function setAttributes(mixed $attributes): static
            {
                return $this;
            }

            public function setStatus(string $status, string $description = ''): static
            {
                return $this;
            }

            public function recordException(Throwable $exception): static
            {
                return $this;
            }

            public function addEvent(string $name, mixed $attributes = null): static
            {
                return $this;
            }

            public function getAttribute(string $key): mixed
            {
                return null;
            }

            public function end(): void
            {
            }
        };
    }

    /**
     * Safely call add on a counter object.
     *
     * @param object     $counter
     * @param int        $amount
     * @param mixed|null $attributes
     */
    private function safeCounterAdd(object $counter, int $amount, mixed $attributes = null): void
    {
        if (method_exists($counter, 'add')) {
            $counter->add($amount, $attributes);
        }
    }

    /**
     * Safely call record on a histogram object.
     *
     * @param object     $histogram
     * @param float      $amount
     * @param mixed|null $attributes
     */
    private function safeHistogramRecord(object $histogram, float $amount, mixed $attributes = null): void
    {
        if (method_exists($histogram, 'record')) {
            $histogram->record($amount, $attributes);
        }
    }
}
