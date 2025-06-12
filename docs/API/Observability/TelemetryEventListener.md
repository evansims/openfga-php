# TelemetryEventListener

Event listener that forwards domain events to the telemetry provider. This decouples business logic from telemetry by using events to communicate what happened without the business logic needing to know about telemetry.

## Namespace

`OpenFGA\Observability`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListener.php)

## Implements

* [`TelemetryEventListenerInterface`](TelemetryEventListenerInterface.md)

## Related Classes

* [TelemetryEventListenerInterface](Observability/TelemetryEventListenerInterface.md) (interface)

## Methods

#### onHttpRequestSent

```php
public function onHttpRequestSent(OpenFGA\Events\HttpRequestSentEvent $event): void

```

Handle HTTP request sent events. Records telemetry data when an HTTP request is sent, including request method, URL, body size, and OpenFGA-specific context like operation, store ID, and model ID.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListener.php#L29)

#### Parameters

| Name     | Type                                                     | Description                 |
| -------- | -------------------------------------------------------- | --------------------------- |
| `$event` | [`HttpRequestSentEvent`](Events/HttpRequestSentEvent.md) | The HTTP request sent event |

#### Returns

`void`

#### onHttpResponseReceived

```php
public function onHttpResponseReceived(OpenFGA\Events\HttpResponseReceivedEvent $event): void

```

Handle HTTP response received events. Records telemetry data when an HTTP response is received, including response status, body size, and any exception information if the request failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListener.php#L46)

#### Parameters

| Name     | Type                                                               | Description                      |
| -------- | ------------------------------------------------------------------ | -------------------------------- |
| `$event` | [`HttpResponseReceivedEvent`](Events/HttpResponseReceivedEvent.md) | The HTTP response received event |

#### Returns

`void`

#### onOperationCompleted

```php
public function onOperationCompleted(OpenFGA\Events\OperationCompletedEvent $event): void

```

Handle operation completed events. Records telemetry data when an OpenFGA operation completes, including success status, operation context, and exception details if the operation failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListener.php#L72)

#### Parameters

| Name     | Type                                                           | Description                   |
| -------- | -------------------------------------------------------------- | ----------------------------- |
| `$event` | [`OperationCompletedEvent`](Events/OperationCompletedEvent.md) | The operation completed event |

#### Returns

`void`

#### onOperationStarted

```php
public function onOperationStarted(OpenFGA\Events\OperationStartedEvent $event): void

```

Handle operation started events. Records telemetry data when an OpenFGA operation begins, including operation type, store context, and model information.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListener.php#L94)

#### Parameters

| Name     | Type                                                       | Description                 |
| -------- | ---------------------------------------------------------- | --------------------------- |
| `$event` | [`OperationStartedEvent`](Events/OperationStartedEvent.md) | The operation started event |

#### Returns

`void`
