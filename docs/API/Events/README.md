# Events

[API Documentation](../README.md) > Events

Event system for cross-cutting concerns like logging and metrics collection.

**Total Components:** 7

## Interfaces

| Name | Description |
|------|-------------|
| [`EventDispatcherInterface`](./EventDispatcherInterface.md) | Event dispatcher interface for handling domain events. The event dispatcher decouples event publi... |
| [`EventInterface`](./EventInterface.md) | Base interface for all domain events. Events represent something significant that happened in the... |

## Classes

| Name | Description |
|------|-------------|
| [`EventDispatcher`](./EventDispatcher.md) | Simple event dispatcher implementation. Manages event listeners and dispatches events to register... |
| [`HttpRequestSentEvent`](./HttpRequestSentEvent.md) | Event fired when an HTTP request is sent to the OpenFGA API. This event contains the outgoing req... |
| [`HttpResponseReceivedEvent`](./HttpResponseReceivedEvent.md) | Event fired when an HTTP response is received from the OpenFGA API. This event contains both the ... |
| [`OperationCompletedEvent`](./OperationCompletedEvent.md) | Event fired when a high-level operation completes. This event tracks the completion of business o... |
| [`OperationStartedEvent`](./OperationStartedEvent.md) | Event fired when a high-level operation starts. This event tracks business operations like check,... |

---

[← Back to API Documentation](../README.md)
