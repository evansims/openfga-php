# TelemetryEventListener

Event listener that forwards domain events to the telemetry provider. This decouples business logic from telemetry by using events to communicate what happened without the business logic needing to know about telemetry.

## Namespace

`OpenFGA\Observability`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListener.php)

## Methods

#### onHttpRequestSent

```php
public function onHttpRequestSent(HttpRequestSentEvent $event): void

```

Handle HTTP request sent events.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListener.php#L29)

#### Parameters

| Name     | Type                   | Description |
| -------- | ---------------------- | ----------- |
| `$event` | `HttpRequestSentEvent` |             |

#### Returns

`void`

#### onHttpResponseReceived

```php
public function onHttpResponseReceived(HttpResponseReceivedEvent $event): void

```

Handle HTTP response received events.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListener.php#L47)

#### Parameters

| Name     | Type                        | Description |
| -------- | --------------------------- | ----------- |
| `$event` | `HttpResponseReceivedEvent` |             |

#### Returns

`void`

#### onOperationCompleted

```php
public function onOperationCompleted(OperationCompletedEvent $event): void

```

Handle operation completed events.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListener.php#L74)

#### Parameters

| Name     | Type                      | Description |
| -------- | ------------------------- | ----------- |
| `$event` | `OperationCompletedEvent` |             |

#### Returns

`void`

#### onOperationStarted

```php
public function onOperationStarted(OperationStartedEvent $event): void

```

Handle operation started events.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryEventListener.php#L97)

#### Parameters

| Name     | Type                    | Description |
| -------- | ----------------------- | ----------- |
| `$event` | `OperationStartedEvent` |             |

#### Returns

`void`
