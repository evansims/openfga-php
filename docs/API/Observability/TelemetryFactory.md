# TelemetryFactory

Factory for creating telemetry providers with OpenTelemetry integration. This factory provides convenient methods for setting up observability with the OpenFGA SDK. It handles the conditional creation of OpenTelemetry providers when the dependencies are available, and falls back to null when they are not. The factory follows the principle of graceful degradation, ensuring that the SDK remains functional even when OpenTelemetry is not installed or configured in the host application.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)

</details>

## Namespace

`OpenFGA\Observability`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Observability/TelemetryFactory.php)
