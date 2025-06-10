# Services

[API Documentation](../README.md) > Services

Business logic services that orchestrate between repositories and external systems.

**Total Components:** 19

## Interfaces

| Name | Description |
|------|-------------|
| [`AssertionServiceInterface`](./AssertionServiceInterface.md) | Service interface for managing OpenFGA authorization model assertions. This service provides busi... |
| [`AuthenticationServiceInterface`](./AuthenticationServiceInterface.md) | Service interface for managing authentication in OpenFGA operations. This service abstracts authe... |
| [`AuthorizationServiceInterface`](./AuthorizationServiceInterface.md) | Service interface for authorization operations. This interface defines methods for all authorizat... |
| [`HttpServiceInterface`](./HttpServiceInterface.md) | Service for handling HTTP communication. This service abstracts HTTP operations from the Client c... |
| [`ModelServiceInterface`](./ModelServiceInterface.md) | Service interface for managing OpenFGA authorization models. This service provides business-focus... |
| [`StoreServiceInterface`](./StoreServiceInterface.md) | Service interface for high-level store operations. This interface provides a business-focused abs... |
| [`TelemetryServiceInterface`](./TelemetryServiceInterface.md) | Service interface for managing telemetry and observability in OpenFGA operations. This service pr... |
| [`TupleFilterServiceInterface`](./TupleFilterServiceInterface.md) | Service for filtering and deduplicating tuple operations. This service encapsulates the business ... |
| [`TupleServiceInterface`](./TupleServiceInterface.md) | Service interface for managing OpenFGA relationship tuples. This service provides business-focuse... |

## Classes

| Name | Description |
|------|-------------|
| [`AssertionService`](./AssertionService.md) | Service implementation for managing OpenFGA authorization model assertions. Provides business-foc... |
| [`AuthenticationService`](./AuthenticationService.md) | Service implementation for managing authentication in OpenFGA operations. This service encapsulat... |
| [`AuthorizationService`](./AuthorizationService.md) | Service implementation for authorization operations. This service handles all authorization-relat... |
| [`EventAwareTelemetryService`](./EventAwareTelemetryService.md) | Event-aware telemetry service that emits domain events. This service extends the base TelemetrySe... |
| [`HttpService`](./HttpService.md) | Default implementation of HttpServiceInterface. This implementation delegates to RequestManager f... |
| [`ModelService`](./ModelService.md) | Service implementation for managing OpenFGA authorization models. Provides business-focused opera... |
| [`StoreService`](./StoreService.md) | Service implementation for high-level store operations. This service provides business-focused ab... |
| [`TelemetryService`](./TelemetryService.md) | Service implementation for managing telemetry and observability in OpenFGA operations. Provides a... |
| [`TupleFilterService`](./TupleFilterService.md) | Default implementation of TupleFilterServiceInterface. Provides efficient duplicate filtering for... |
| [`TupleService`](./TupleService.md) | Service implementation for managing OpenFGA relationship tuples. Provides business-focused operat... |

---

[← Back to API Documentation](../README.md)
