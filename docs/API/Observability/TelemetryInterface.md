# TelemetryInterface

Interface for OpenTelemetry integration in the OpenFGA SDK. This interface provides methods for instrumenting OpenFGA operations with observability features including distributed tracing, metrics collection, and structured logging. Implementations should integrate with OpenTelemetry or other observability platforms to provide insights into SDK performance and operation outcomes. The interface supports both automatic instrumentation of HTTP requests and business-level instrumentation of OpenFGA API operations. All methods are designed to be safe to call even when no telemetry backend is configured, ensuring the SDK remains functional without observability dependencies.

## Namespace

`OpenFGA\Observability`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryInterface.php)

## Methods

#### endHttpRequest

```php
public function endHttpRequest(
    object|null $span,
    ResponseInterface|null $response = NULL,
    Throwable|null $exception = NULL,
): void

```

End tracing for an HTTP request. Completes the HTTP request span, recording the response status and any errors that occurred. The span should include standard HTTP response attributes such as status code and response size.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryInterface.php#L41)

#### Parameters

| Name         | Type                                                                | Description                                         |
| ------------ | ------------------------------------------------------------------- | --------------------------------------------------- |
| `$span`      | `object` &#124; `null`                                              | The span identifier returned by startHttpRequest()  |
| `$response`  | [`ResponseInterface`](Responses/ResponseInterface.md) &#124; `null` | The HTTP response received, if any                  |
| `$exception` | `Throwable` &#124; `null`                                           | Optional exception that occurred during the request |

#### Returns

`void`

#### endOperation

```php
public function endOperation(
    object|null $span,
    bool $success,
    Throwable|null $exception = NULL,
    array<string, mixed> $attributes = [],
): void

```

End tracing for an OpenFGA API operation. Completes the trace span started with startOperation(), recording the operation outcome and any relevant metrics. If an exception occurred during the operation, it should be recorded in the span.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryInterface.php#L59)

#### Parameters

| Name          | Type                             | Description                                           |
| ------------- | -------------------------------- | ----------------------------------------------------- |
| `$span`       | `object` &#124; `null`           | The span identifier returned by startOperation()      |
| `$success`    | `bool`                           | Whether the operation completed successfully          |
| `$exception`  | `Throwable` &#124; `null`        | Optional exception that occurred during the operation |
| `$attributes` | `array&lt;`string`, `mixed`&gt;` |                                                       |

#### Returns

`void`

#### recordAuthenticationEvent

```php
public function recordAuthenticationEvent(
    string $event,
    bool $success,
    float $duration,
    array<string, mixed> $attributes = [],
): void

```

Record authentication events. Records metrics and traces related to authentication flows, including token acquisition, refresh operations, and authentication failures. This helps monitor authentication performance and troubleshoot auth issues.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryInterface.php#L78)

#### Parameters

| Name          | Type                             | Description                                                                                                    |
| ------------- | -------------------------------- | -------------------------------------------------------------------------------------------------------------- |
| `$event`      | `string`                         | The authentication event type (&#039;token_request&#039;, &#039;token_refresh&#039;, &#039;auth_failure&#039;) |
| `$success`    | `bool`                           | Whether the authentication event was successful                                                                |
| `$duration`   | `float`                          | The duration of the authentication operation in seconds                                                        |
| `$attributes` | `array&lt;`string`, `mixed`&gt;` |                                                                                                                |

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryInterface.php#L97)

#### Parameters

| Name           | Type     | Description                                                                                 |
| -------------- | -------- | ------------------------------------------------------------------------------------------- |
| `$endpoint`    | `string` | The API endpoint this circuit breaker protects                                              |
| `$state`       | `string` | The new circuit breaker state (&#039;open&#039;, &#039;closed&#039;, &#039;half_open&#039;) |
| `$failures`    | `int`    | The current failure count                                                                   |
| `$failureRate` | `float`  | The current failure rate (0.0 to 1.0)                                                       |

#### Returns

`void`

#### recordOperationMetrics

```php
public function recordOperationMetrics(
    string $operation,
    float $duration,
    StoreInterface|string $store,
    AuthorizationModelInterface|string|null $model = NULL,
    array<string, mixed> $attributes = [],
): void

```

Record performance metrics for OpenFGA operations. Records timing and throughput metrics for OpenFGA API operations, allowing monitoring of operation latency and identifying performance bottlenecks or degradations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryInterface.php#L117)

#### Parameters

| Name          | Type                                                                                                 | Description                       |
| ------------- | ---------------------------------------------------------------------------------------------------- | --------------------------------- |
| `$operation`  | `string`                                                                                             | The OpenFGA operation name        |
| `$duration`   | `float`                                                                                              | The operation duration in seconds |
| `$store`      | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                                         | The store being operated on       |
| `$model`      | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` &#124; `null` | The authorization model used      |
| `$attributes` | `array&lt;`string`, `mixed`&gt;`                                                                     |                                   |

#### Returns

`void`

#### recordRetryAttempt

```php
public function recordRetryAttempt(
    string $endpoint,
    int $attempt,
    int $delayMs,
    string $outcome,
    Throwable|null $exception = NULL,
): void

```

Record retry attempt metrics. Records metrics about retry attempts, including the retry count, delay, and eventual outcome. This helps track the reliability and performance of API requests under various network conditions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryInterface.php#L138)

#### Parameters

| Name         | Type                      | Description                                                                               |
| ------------ | ------------------------- | ----------------------------------------------------------------------------------------- |
| `$endpoint`  | `string`                  | The API endpoint being retried                                                            |
| `$attempt`   | `int`                     | The current attempt number (1-based)                                                      |
| `$delayMs`   | `int`                     | The delay before this attempt in milliseconds                                             |
| `$outcome`   | `string`                  | The outcome of this attempt (&#039;success&#039;, &#039;failure&#039;, &#039;retry&#039;) |
| `$exception` | `Throwable` &#124; `null` | Optional exception from this attempt                                                      |

#### Returns

`void`

#### recordSpan

```php
public function recordSpan(string $name, array<string, mixed> $attributes = []): void

```

Record a telemetry span with attributes. Records a complete telemetry span for events that don&#039;t require start/end semantics. This is useful for event-driven telemetry where the event represents a point in time rather than a duration.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryInterface.php#L156)

#### Parameters

| Name          | Type                             | Description   |
| ------------- | -------------------------------- | ------------- |
| `$name`       | `string`                         | The span name |
| `$attributes` | `array&lt;`string`, `mixed`&gt;` |               |

#### Returns

`void`

#### startHttpRequest

```php
public function startHttpRequest(RequestInterface $request): object|null

```

Start tracing an HTTP request. Creates a new trace span for an outgoing HTTP request to the OpenFGA API. The span should follow OpenTelemetry semantic conventions for HTTP client operations, including standard HTTP attributes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryInterface.php#L168)

#### Parameters

| Name       | Type                                               | Description                 |
| ---------- | -------------------------------------------------- | --------------------------- |
| `$request` | [`RequestInterface`](Requests/RequestInterface.md) | The HTTP request being sent |

#### Returns

`object` &#124; `null` — A span identifier or context that can be passed to endHttpRequest()

#### startOperation

```php
public function startOperation(
    string $operation,
    StoreInterface|string $store,
    AuthorizationModelInterface|string|null $model = NULL,
    array<string, mixed> $attributes = [],
): object|null

```

Start tracing an OpenFGA API operation. Creates a new trace span for a high-level OpenFGA operation such as check, expand, or write operations. The span should include relevant attributes such as store ID, authorization model ID, and operation-specific metadata.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryInterface.php#L183)

#### Parameters

| Name          | Type                                                                                                 | Description                                                                                        |
| ------------- | ---------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------- |
| `$operation`  | `string`                                                                                             | The OpenFGA operation name (e.g., &#039;check&#039;, &#039;expand&#039;, &#039;write_tuples&#039;) |
| `$store`      | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                                         | The store being operated on                                                                        |
| `$model`      | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` &#124; `null` | The authorization model being used                                                                 |
| `$attributes` | `array&lt;`string`, `mixed`&gt;`                                                                     |                                                                                                    |

#### Returns

`object` &#124; `null` — A span identifier or context that can be passed to endOperation()
