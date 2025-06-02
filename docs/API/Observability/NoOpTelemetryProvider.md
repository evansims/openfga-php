# NoOpTelemetryProvider

No-operation telemetry provider for when OpenTelemetry is not available. This class provides a safe fallback implementation of the TelemetryInterface that performs no operations. It ensures the OpenFGA SDK remains fully functional even when OpenTelemetry dependencies are not installed or configured, maintaining backward compatibility and optional observability. All methods in this class are designed to be as lightweight as possible, introducing minimal overhead when telemetry is disabled. The class follows the null object pattern to eliminate the need for conditional checks throughout the SDK codebase.

## Namespace

`OpenFGA\Observability`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Observability/NoOpTelemetryProvider.php)

## Implements

* [`TelemetryInterface`](TelemetryInterface.md)

## Methods

#### endHttpRequest

```php
public function endHttpRequest(
    mixed $span,
    ?Psr\Http\Message\ResponseInterface $response = NULL,
    ?Throwable $exception = NULL,
): void

```

End tracing for an HTTP request. Completes the HTTP request span, recording the response status and any errors that occurred. The span should include standard HTTP response attributes such as status code and response size.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/NoOpTelemetryProvider.php#L31)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$span` | `mixed` | The span identifier returned by startHttpRequest() |

| `$response` | `Psr\Http\Message\ResponseInterface` &#124; `null` | The HTTP response received, if any |

| `$exception` | `Throwable` &#124; `null` | Optional exception that occurred during the request |

#### Returns

`void`

#### endOperation

```php
public function endOperation(
    mixed $span,
    bool $success,
    ?Throwable $exception = NULL,
    array $attributes = [],
): void

```

End tracing for an OpenFGA API operation. Completes the trace span started with startOperation(), recording the operation outcome and any relevant metrics. If an exception occurred during the operation, it should be recorded in the span.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/NoOpTelemetryProvider.php#L43)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$span` | `mixed` | The span identifier returned by startOperation() |

| `$success` | `bool` | Whether the operation completed successfully |

| `$exception` | `Throwable` &#124; `null` | Optional exception that occurred during the operation |

| `$attributes` | `array` |  |

#### Returns

`void`

#### recordAuthenticationEvent

```php
public function recordAuthenticationEvent(
    string $event,
    bool $success,
    float $duration,
    array $attributes = [],
): void

```

Record authentication events. Records metrics and traces related to authentication flows, including token acquisition, refresh operations, and authentication failures. This helps monitor authentication performance and troubleshoot auth issues.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/NoOpTelemetryProvider.php#L56)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$event` | `string` | The authentication event type (&#039;token_request&#039;, &#039;token_refresh&#039;, &#039;auth_failure&#039;) |

| `$success` | `bool` | Whether the authentication event was successful |

| `$duration` | `float` | The duration of the authentication operation in seconds |

| `$attributes` | `array` |  |

#### Returns

`void`

#### recordCircuitBreakerState

```php
public function recordCircuitBreakerState(
    string $endpoint,
    string $state,
    int $failures,
    float $failureRate,
): void

```

Record circuit breaker state changes. Records metrics about circuit breaker state transitions and failure rates. This helps monitor the health of individual API endpoints and the SDK&#039;s resilience mechanisms.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/NoOpTelemetryProvider.php#L69)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$endpoint` | `string` | The API endpoint this circuit breaker protects |

| `$state` | `string` | The new circuit breaker state (&#039;open&#039;, &#039;closed&#039;, &#039;half_open&#039;) |

| `$failures` | `int` | The current failure count |

| `$failureRate` | `float` | The current failure rate (0.0 to 1.0) |

#### Returns

`void`

#### recordOperationMetrics

```php
public function recordOperationMetrics(
    string $operation,
    float $duration,
    OpenFGA\Models\StoreInterface|string $store,
    ?OpenFGA\Models\AuthorizationModelInterface|string|null $model = NULL,
    array $attributes = [],
): void

```

Record performance metrics for OpenFGA operations. Records timing and throughput metrics for OpenFGA API operations, allowing monitoring of operation latency and identifying performance bottlenecks or degradations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/NoOpTelemetryProvider.php#L82)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$operation` | `string` | The OpenFGA operation name |

| `$duration` | `float` | The operation duration in seconds |

| `$store` | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` | The store being operated on |

| `$model` | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `null` &#124; `string` &#124; `null` | The authorization model used |

| `$attributes` | `array` |  |

#### Returns

`void`

#### recordRetryAttempt

```php
public function recordRetryAttempt(
    string $endpoint,
    int $attempt,
    int $delayMs,
    string $outcome,
    ?Throwable $exception = NULL,
): void

```

Record retry attempt metrics. Records metrics about retry attempts, including the retry count, delay, and eventual outcome. This helps track the reliability and performance of API requests under various network conditions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/NoOpTelemetryProvider.php#L96)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$endpoint` | `string` | The API endpoint being retried |

| `$attempt` | `int` | The current attempt number (1-based) |

| `$delayMs` | `int` | The delay before this attempt in milliseconds |

| `$outcome` | `string` | The outcome of this attempt (&#039;success&#039;, &#039;failure&#039;, &#039;retry&#039;) |

| `$exception` | `Throwable` &#124; `null` | Optional exception from this attempt |

#### Returns

`void`

#### startHttpRequest

```php
public function startHttpRequest(Psr\Http\Message\RequestInterface $request): ?null

```

Start tracing an HTTP request. Creates a new trace span for an outgoing HTTP request to the OpenFGA API. The span should follow OpenTelemetry semantic conventions for HTTP client operations, including standard HTTP attributes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/NoOpTelemetryProvider.php#L110)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$request` | `Psr\Http\Message\RequestInterface` | The HTTP request being sent |

#### Returns

`null` &#124; `null` — A span identifier or context that can be passed to endHttpRequest()

#### startOperation

```php
public function startOperation(
    string $operation,
    OpenFGA\Models\StoreInterface|string $store,
    ?OpenFGA\Models\AuthorizationModelInterface|string|null $model = NULL,
    array $attributes = [],
): ?null

```

Start tracing an OpenFGA API operation. Creates a new trace span for a high-level OpenFGA operation such as check, expand, or write operations. The span should include relevant attributes such as store ID, authorization model ID, and operation-specific metadata.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/NoOpTelemetryProvider.php#L119)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$operation` | `string` | The OpenFGA operation name (e.g., &#039;check&#039;, &#039;expand&#039;, &#039;write_tuples&#039;) |

| `$store` | [`StoreInterface`](Models/StoreInterface.md) &#124; `string` | The store being operated on |

| `$model` | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `null` &#124; `string` &#124; `null` | The authorization model being used |

| `$attributes` | `array` |  |

#### Returns

`null` &#124; `null` — A span identifier or context that can be passed to endOperation()
