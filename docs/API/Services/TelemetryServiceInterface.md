# TelemetryServiceInterface

Service interface for managing telemetry and observability in OpenFGA operations. This service provides a higher-level abstraction over the telemetry infrastructure, handling the creation, management, and coordination of telemetry spans, metrics, and events. It simplifies telemetry usage by providing business-focused methods that handle common patterns like operation timing and error tracking. ## Core Functionality The service manages the lifecycle of telemetry data for: - HTTP requests and responses with automatic span management - Business operations with timing and success/failure tracking - Error and exception handling with contextual information - Performance metrics and operational insights ## Usage Example ```php $telemetryService = new TelemetryService($telemetryProvider); Track a complete operation $context = $telemetryService-&gt;startOperation(&#039;check&#039;, $store, $model); try { $result = $businessLogic(); $telemetryService-&gt;recordSuccess($context, $result); return $result; } catch (Throwable $error) { $telemetryService-&gt;recordFailure($context, $error); throw $error; } ```

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
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

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/TelemetryServiceInterface.php)

## Related Classes

* [TelemetryService](Services/TelemetryService.md) (implementation)

## Methods

#### recordAuthenticationEvent

```php
public function recordAuthenticationEvent(
    string $event,
    bool $success,
    float $duration,
    array<string, mixed> $attributes = [],
): void

```

Record an authentication event with duration and outcome. Tracks authentication-related operations including token acquisition, renewal, and validation. Provides insights into authentication performance and failure patterns.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TelemetryServiceInterface.php#L60)

#### Parameters

| Name          | Type                             | Description                      |
| ------------- | -------------------------------- | -------------------------------- |
| `$event`      | `string`                         | The authentication event type    |
| `$success`    | `bool`                           | Whether the event was successful |
| `$duration`   | `float`                          | Event duration in seconds        |
| `$attributes` | `array&lt;`string`, `mixed`&gt;` |                                  |

#### Returns

`void`

#### recordFailure

```php
public function recordFailure(TelemetryContext $context, Throwable $exception, mixed $result = NULL): void

```

Record a failed operation with error details. Completes an operation context with failure information, including exception details and any additional error context. This provides structured error tracking for debugging and monitoring.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TelemetryServiceInterface.php#L78)

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
    RequestInterface $request,
    ResponseInterface|null $response = NULL,
    Throwable|null $exception = NULL,
    float|null $duration = NULL,
): void

```

Record an HTTP request/response pair with automatic span management. Handles the complete lifecycle of HTTP request telemetry, including span creation, timing, and completion with response or error details. Ideal for tracking individual API calls.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TelemetryServiceInterface.php#L96)

#### Parameters

| Name         | Type                                                                | Description                       |
| ------------ | ------------------------------------------------------------------- | --------------------------------- |
| `$request`   | [`RequestInterface`](Requests/RequestInterface.md)                  | The HTTP request being tracked    |
| `$response`  | [`ResponseInterface`](Responses/ResponseInterface.md) &#124; `null` | The HTTP response received        |
| `$exception` | `Throwable` &#124; `null`                                           | Optional exception that occurred  |
| `$duration`  | `float` &#124; `null`                                               | Optional manual duration override |

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

Record operational metrics for performance monitoring. Tracks operation-level metrics including timing, throughput, and contextual information about stores and models. Used for performance analysis and capacity planning.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TelemetryServiceInterface.php#L116)

#### Parameters

| Name          | Type                                                                                                 | Description                   |
| ------------- | ---------------------------------------------------------------------------------------------------- | ----------------------------- |
| `$operation`  | `string`                                                                                             | The operation name            |
| `$duration`   | `float`                                                                                              | Operation duration in seconds |
| `$store`      | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                                         | The store context             |
| `$model`      | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` &#124; `null` | Optional model context        |
| `$attributes` | `array&lt;`string`, `mixed`&gt;`                                                                     |                               |

#### Returns

`void`

#### recordSuccess

```php
public function recordSuccess(TelemetryContext $context, mixed $result = NULL): void

```

Record a successful operation with results. Completes an operation context with success information and any relevant result data. This tracks successful operation patterns and performance characteristics.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TelemetryServiceInterface.php#L134)

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
    StoreInterface|string $store,
    AuthorizationModelInterface|string|null $model = NULL,
    array<string, mixed> $attributes = [],
): TelemetryContext

```

Start tracking a business operation. Creates a new telemetry context for tracking a complete business operation including timing, success/failure status, and contextual information. Returns a context object that should be passed to recordSuccess/recordFailure.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/TelemetryServiceInterface.php#L152)

#### Parameters

| Name          | Type                                                                                                 | Description            |
| ------------- | ---------------------------------------------------------------------------------------------------- | ---------------------- |
| `$operation`  | `string`                                                                                             | The operation name     |
| `$store`      | [`StoreInterface`](Models/StoreInterface.md) &#124; `string`                                         | The store context      |
| `$model`      | [`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `string` &#124; `null` | Optional model context |
| `$attributes` | `array&lt;`string`, `mixed`&gt;`                                                                     |                        |

#### Returns

`TelemetryContext` — Context for completing the operation tracking
