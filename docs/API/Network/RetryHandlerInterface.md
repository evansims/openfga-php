# RetryHandlerInterface

Retry handler interface for advanced HTTP request retry strategies. This interface defines the contract for retry handler implementations that provide sophisticated retry logic for HTTP requests, including exponential backoff, jitter, circuit breaker integration, and server-header-aware delays. Retry handlers categorize errors and apply appropriate retry strategies: - Network errors: Fast initial retry with exponential backoff - Rate limits (429): Honor server timing headers exactly - Server errors (5xx): Standard exponential backoff - Maintenance (503): Extended delays for service recovery The implementation should respect server-provided timing via Retry-After and rate limit headers while providing fallback logic for cases where such headers are unavailable.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Related Classes](#related-classes)
* [Methods](#methods)

* [Other](#other)
    * [`executeWithRetry()`](#executewithretry)

## Namespace

`OpenFGA\Network`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/RetryHandlerInterface.php)

## Related Classes

* [RetryHandler](Network/RetryHandler.md) (implementation)

## Methods

#### executeWithRetry

```php
public function executeWithRetry(
    callable $requestExecutor,
    RequestInterface $request,
    string $endpoint,
): ResponseInterface

```

Execute an HTTP request with automatic retry logic. Performs the HTTP request with intelligent retry behavior based on error type, server headers, and circuit breaker state. The method tracks attempt counts, calculates appropriate delays, and respects server-provided timing information. The implementation should: - Check circuit breaker state before attempting requests - Apply exponential backoff with jitter to prevent thundering herd - Respect server-provided timing headers (Retry-After, X-Rate-Limit-Reset) - Handle different error types with appropriate retry strategies - Consider request method idempotency for retry decisions - Track failures and successes with the circuit breaker

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RetryHandlerInterface.php#L55)

#### Parameters

| Name               | Type                                               | Description                                   |
| ------------------ | -------------------------------------------------- | --------------------------------------------- |
| `$requestExecutor` | `callable`                                         |                                               |
| `$request`         | [`RequestInterface`](Requests/RequestInterface.md) | The original HTTP request for context         |
| `$endpoint`        | `string`                                           | The endpoint URL for circuit breaker tracking |

#### Returns

[`ResponseInterface`](Responses/ResponseInterface.md) — The successful HTTP response
