# Network

[API Documentation](../README.md) > Network

HTTP client abstractions, retry strategies, and low-level networking components.

**Total Components:** 19

## Interfaces

| Name | Description |
|------|-------------|
| [`CircuitBreakerInterface`](./CircuitBreakerInterface.md) | Circuit breaker interface for preventing cascade failures in distributed systems. This interface ... |
| [`ConcurrentExecutorInterface`](./ConcurrentExecutorInterface.md) | Interface for concurrent task execution. This interface defines the contract for executing multip... |
| [`HttpClientInterface`](./HttpClientInterface.md) | HTTP client interface for sending HTTP requests. This interface abstracts the HTTP client impleme... |
| [`RequestContextInterface`](./RequestContextInterface.md) | Represents the context for an HTTP request to the OpenFGA API. This interface encapsulates all th... |
| [`RequestManagerInterface`](./RequestManagerInterface.md) | Manages HTTP requests and responses for OpenFGA API communication. This interface defines the cor... |
| [`RetryHandlerInterface`](./RetryHandlerInterface.md) | Retry handler interface for advanced HTTP request retry strategies. This interface defines the co... |
| [`RetryStrategyInterface`](./RetryStrategyInterface.md) | Interface for implementing retry strategies. This interface defines the contract for different re... |

## Classes

| Name | Description |
|------|-------------|
| [`BatchRequestProcessor`](./BatchRequestProcessor.md) | Handles batch processing of write tuple requests. This class encapsulates the logic for processin... |
| [`CircuitBreaker`](./CircuitBreaker.md) | Circuit breaker implementation for preventing cascade failures in distributed systems. This class... |
| [`ExponentialBackoffRetryStrategy`](./ExponentialBackoffRetryStrategy.md) | Exponential backoff retry strategy implementation. This strategy implements exponential backoff w... |
| [`FiberConcurrentExecutor`](./FiberConcurrentExecutor.md) | Fiber-based concurrent executor implementation. This implementation uses PHP 8.1+ Fibers to execu... |
| [`ParallelTaskExecutor`](./ParallelTaskExecutor.md) | Executes tasks in parallel using the RequestManager infrastructure. This class provides a clean a... |
| [`PsrHttpClient`](./PsrHttpClient.md) | PSR-18 compliant HTTP client implementation. This implementation wraps any PSR-18 compatible HTTP... |
| [`RequestContext`](./RequestContext.md) | Implementation of request context for OpenFGA API operations. This class provides a concrete impl... |
| [`RequestManager`](./RequestManager.md) | Concrete implementation of HTTP request management for OpenFGA API communication. This class prov... |
| [`RequestManagerFactory`](./RequestManagerFactory.md) | Factory for creating RequestManager instances. This factory encapsulates the creation of RequestM... |
| [`RetryHandler`](./RetryHandler.md) | Concrete implementation of the retry handler using standard sleep delays. This final class provid... |
| [`SimpleConcurrentExecutor`](./SimpleConcurrentExecutor.md) | Simple concurrent executor implementation. This implementation provides a fallback for environmen... |

## Enumerations

| Name | Description |
|------|-------------|
| [`RequestMethod`](./RequestMethod.md) | HTTP request methods supported by the OpenFGA API. This enum defines the specific HTTP methods us... |

---

[← Back to API Documentation](../README.md)
