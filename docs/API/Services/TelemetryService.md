# TelemetryService

Service implementation for managing telemetry and observability in OpenFGA operations. Provides a higher-level abstraction over the telemetry infrastructure, simplifying the creation and management of telemetry spans, metrics, and events. This service handles common telemetry patterns and provides business-focused methods for tracking operations, performance, and errors.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Related Classes](#related-classes)
* [Methods](#methods)

* [Other](#other)
    * [`recordAuthenticationEvent()`](#recordauthenticationevent)
    * [`recordFailure()`](#recordfailure)
    * [`recordHttpRequest()`](#recordhttprequest)
    * [`recordOperationMetrics()`](#recordoperationmetrics)
    * [`recordSuccess()`](#recordsuccess)
    * [`startOperation()`](#startoperation)

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/TelemetryService.php)

## Implements

* [`TelemetryServiceInterface`](TelemetryServiceInterface.md)

## Related Classes

* [TelemetryServiceInterface](Services/TelemetryServiceInterface.md) (interface)

## Methods

#### recordAuthenticationEvent

```php
public function recordAuthenticationEvent(
    string $event,
    bool $success,
    float $duration,
    array $attributes = [],
): void

```

Record an authentication event with duration and outcome. Tracks authentication-related operations including token acquisition, renewal, and validation. Provides insights into authentication performance and failure patterns.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TelemetryService.php#L40)

#### Parameters

| Name          | Type     | Description                      |
| ------------- | -------- | -------------------------------- |
| `$event`      | `string` | The authentication event type    |
| `$success`    | `bool`   | Whether the event was successful |
| `$duration`   | `float`  | Event duration in seconds        |
| `$attributes` | `array`  |                                  |

#### Returns

`void`

#### recordFailure

```php
public function recordFailure(
    OpenFGA\Services\TelemetryContext $context,
    Throwable $exception,
    mixed $result = NULL,
): void

```

Record a failed operation with error details. Completes an operation context with failure information, including exception details and any additional error context. This provides structured error tracking for debugging and monitoring.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TelemetryService.php#L53)

#### Parameters

| Name         | Type               | Description                                 |
| ------------ | ------------------ | ------------------------------------------- |
| `$context`   | `TelemetryContext` | The operation context from startOperation() |
| `$exception` | `Throwable`        | The exception that caused the failure       |
| `$result`    | `mixed`            | Optional partial result data                |

#### Returns

`void`

#### recordHttpRequest

```php
public function recordHttpRequest(
    Psr\Http\Message\RequestInterface $request,
    ?Psr\Http\Message\ResponseInterface $response = NULL,
    ?Throwable $exception = NULL,
    ?float $duration = NULL,
): void

```

Record an HTTP request/response pair with automatic span management. Handles the complete lifecycle of HTTP request telemetry, including span creation, timing, and completion with response or error details. Ideal for tracking individual API calls.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TelemetryService.php#L95)

#### Parameters

| Name         | Type                                               | Description                       |
| ------------ | -------------------------------------------------- | --------------------------------- |
| `$request`   | `Psr\Http\Message\RequestInterface`                | The HTTP request being tracked    |
| `$response`  | `Psr\Http\Message\ResponseInterface` &#124; `null` | The HTTP response received        |
| `$exception` | `Throwable` &#124; `null`                          | Optional exception that occurred  |
| `$duration`  | `float` &#124; `null`                              | Optional manual duration override |

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

Record operational metrics for performance monitoring. Tracks operation-level metrics including timing, throughput, and contextual information about stores and models. Used for performance analysis and capacity planning.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TelemetryService.php#L112)

#### Parameters

| Name          | Type                                                                                                               | Description                   |
| ------------- | ------------------------------------------------------------------------------------------------------------------ | ----------------------------- |
| `$operation`  | `string`                                                                                                           | The operation name            |
| `$duration`   | `float`                                                                                                            | Operation duration in seconds |
| `$store`      | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                                                       | The store context             |
| `$model`      | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `null` &#124; `string` &#124; `null` | Optional model context        |
| `$attributes` | `array`                                                                                                            |                               |

#### Returns

`void`

#### recordSuccess

```php
public function recordSuccess(OpenFGA\Services\TelemetryContext $context, mixed $result = NULL): void

```

Record a successful operation with results. Completes an operation context with success information and any relevant result data. This tracks successful operation patterns and performance characteristics.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TelemetryService.php#L126)

#### Parameters

| Name       | Type               | Description                                 |
| ---------- | ------------------ | ------------------------------------------- |
| `$context` | `TelemetryContext` | The operation context from startOperation() |
| `$result`  | `mixed`            | The operation result data                   |

#### Returns

`void`

#### startOperation

```php
public function startOperation(
    string $operation,
    OpenFGA\Models\StoreInterface|string $store,
    ?OpenFGA\Models\AuthorizationModelInterface|string|null $model = NULL,
    array $attributes = [],
): OpenFGA\Services\TelemetryContext

```

Start tracking a business operation. Creates a new telemetry context for tracking a complete business operation including timing, success/failure status, and contextual information. Returns a context object that should be passed to recordSuccess/recordFailure.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TelemetryService.php#L159)

#### Parameters

| Name          | Type                                                                                                               | Description            |
| ------------- | ------------------------------------------------------------------------------------------------------------------ | ---------------------- |
| `$operation`  | `string`                                                                                                           | The operation name     |
| `$store`      | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                                                       | The store context      |
| `$model`      | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `null` &#124; `string` &#124; `null` | Optional model context |
| `$attributes` | `array`                                                                                                            |                        |

#### Returns

`TelemetryContext` — Context for completing the operation tracking
