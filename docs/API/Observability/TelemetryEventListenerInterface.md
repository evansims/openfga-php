# TelemetryEventListenerInterface

Interface for event listeners that forward domain events to telemetry providers. This interface defines the contract for handling telemetry-related events throughout the OpenFGA client lifecycle, enabling observability without tightly coupling business logic to telemetry concerns.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [Other](#other)
  - [`onHttpRequestSent()`](#onhttprequestsent)
  - [`onHttpResponseReceived()`](#onhttpresponsereceived)
  - [`onOperationCompleted()`](#onoperationcompleted)
  - [`onOperationStarted()`](#onoperationstarted)

## Namespace

`OpenFGA\Observability`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListenerInterface.php)

## Related Classes

- [TelemetryEventListener](Observability/TelemetryEventListener.md) (implementation)

## Methods

#### onHttpRequestSent

```php
public function onHttpRequestSent(HttpRequestSentEvent $event): void

```

Handle HTTP request sent events. Records telemetry data when an HTTP request is sent, including request method, URL, body size, and OpenFGA-specific context like operation, store ID, and model ID.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListenerInterface.php#L27)

#### Parameters

| Name     | Type                   | Description                 |
| -------- | ---------------------- | --------------------------- |
| `$event` | `HttpRequestSentEvent` | The HTTP request sent event |

#### Returns

`void`

#### onHttpResponseReceived

```php
public function onHttpResponseReceived(HttpResponseReceivedEvent $event): void

```

Handle HTTP response received events. Records telemetry data when an HTTP response is received, including response status, body size, and any exception information if the request failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListenerInterface.php#L38)

#### Parameters

| Name     | Type                        | Description                      |
| -------- | --------------------------- | -------------------------------- |
| `$event` | `HttpResponseReceivedEvent` | The HTTP response received event |

#### Returns

`void`

#### onOperationCompleted

```php
public function onOperationCompleted(OperationCompletedEvent $event): void

```

Handle operation completed events. Records telemetry data when an OpenFGA operation completes, including success status, operation context, and exception details if the operation failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListenerInterface.php#L49)

#### Parameters

| Name     | Type                      | Description                   |
| -------- | ------------------------- | ----------------------------- |
| `$event` | `OperationCompletedEvent` | The operation completed event |

#### Returns

`void`

#### onOperationStarted

```php
public function onOperationStarted(OperationStartedEvent $event): void

```

Handle operation started events. Records telemetry data when an OpenFGA operation begins, including operation type, store context, and model information.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListenerInterface.php#L59)

#### Parameters

| Name     | Type                    | Description                 |
| -------- | ----------------------- | --------------------------- |
| `$event` | `OperationStartedEvent` | The operation started event |

#### Returns

`void`
