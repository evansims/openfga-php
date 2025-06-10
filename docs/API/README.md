# OpenFGA SDK

**Total Components:** 3

## Subdirectories

| Directory | Description |
|-----------|-------------|
| [`Authentication`](./Authentication/README.md) | Authentication providers and token management for OpenFGA API access. |
| [`DI`](./DI/README.md) | Dependency injection container and service provider for framework integration. |
| [`Events`](./Events/README.md) | Event system for cross-cutting concerns like logging and metrics collection. |
| [`Exceptions`](./Exceptions/README.md) | Exception hierarchy for type-safe error handling throughout the SDK. |
| [`Integration`](./Integration/README.md) | Framework integration helpers and service providers. |
| [`Language`](./Language/README.md) | DSL parser and transformer for human-readable authorization models. |
| [`Models`](./Models/README.md) | Domain models representing OpenFGA entities like stores, tuples, and authorization models. |
| [`Network`](./Network/README.md) | HTTP client abstractions, retry strategies, and low-level networking components. |
| [`Observability`](./Observability/README.md) | Telemetry providers and monitoring integrations for operational visibility. |
| [`Repositories`](./Repositories/README.md) | Data access interfaces and implementations for managing OpenFGA resources. |
| [`Requests`](./Requests/README.md) | Request objects for all OpenFGA API operations. |
| [`Responses`](./Responses/README.md) | Response objects containing API results and metadata. |
| [`Results`](./Results/README.md) | Result pattern implementation for functional error handling without exceptions. |
| [`Schemas`](./Schemas/README.md) | JSON schema validation for ensuring data integrity and type safety. |
| [`Services`](./Services/README.md) | Business logic services that orchestrate between repositories and external systems. |
| [`Translation`](./Translation/README.md) | Internationalization support and message translation utilities. |

## Interfaces

| Name | Description |
|------|-------------|
| [`ClientInterface`](./ClientInterface.md) | OpenFGA Client Interface for relationship-based access control operations. This interface defines... |

## Classes

| Name | Description |
|------|-------------|
| [`Client`](./Client.md) | OpenFGA Client implementation for relationship-based access control operations. This client provi... |

## Enumerations

| Name | Description |
|------|-------------|
| [`Messages`](./Messages.md) | Centralized message keys for all exception messages in the OpenFGA PHP SDK. This enum provides ty... |

