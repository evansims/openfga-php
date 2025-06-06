# Observability

[API Documentation](../README.md) > Observability

Telemetry providers and monitoring integrations for operational visibility.

**Total Components:** 5

## Interfaces

| Name | Description |
|------|-------------|
| [`TelemetryInterface`](./TelemetryInterface.md) | Interface for OpenTelemetry integration in the OpenFGA SDK. This interface provides methods for i... |

## Classes

| Name | Description |
|------|-------------|
| [`NoOpTelemetryProvider`](./NoOpTelemetryProvider.md) | No-operation telemetry provider for when OpenTelemetry is not available. This class provides a sa... |
| [`OpenTelemetryProvider`](./OpenTelemetryProvider.md) | OpenTelemetry implementation for OpenFGA SDK observability. This class provides comprehensive tel... |
| [`TelemetryEventListener`](./TelemetryEventListener.md) | Event listener that forwards domain events to the telemetry provider. This decouples business log... |
| [`TelemetryFactory`](./TelemetryFactory.md) | Factory for creating telemetry providers with OpenTelemetry integration. This factory provides co... |

---

[← Back to API Documentation](../README.md)
