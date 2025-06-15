# RetryHandler

Concrete implementation of the retry handler using standard sleep delays. This final class provides the default implementation of the retry handler that uses actual sleep delays for production use. For testing or custom delay implementations, extend AbstractRetryHandler instead.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`executeWithRetry()`](#executewithretry)

</details>

## Namespace

`OpenFGA\Network`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/RetryHandler.php)

## Implements

- [`RetryHandlerInterface`](RetryHandlerInterface.md)

## Related Classes

- [RetryHandlerInterface](Network/RetryHandlerInterface.md) (interface)

## Methods

### executeWithRetry

```php
public function executeWithRetry(
    callable $requestExecutor,
    Psr\Http\Message\RequestInterface $request,
    string $endpoint,
): Psr\Http\Message\ResponseInterface

```

Execute an HTTP request with automatic retry logic. Performs the HTTP request with intelligent retry behavior based on error type, server headers, and circuit breaker state. The method tracks attempt counts, calculates appropriate delays, and respects server-provided timing information. The implementation should: - Check circuit breaker state before attempting requests - Apply exponential backoff with jitter to prevent thundering herd - Respect server-provided timing headers (Retry-After, X-Rate-Limit-Reset) - Handle different error types with appropriate retry strategies - Consider request method idempotency for retry decisions - Track failures and successes with the circuit breaker

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/AbstractRetryHandler.php#L104)

#### Parameters

| Name               | Type                                | Description                                   |
| ------------------ | ----------------------------------- | --------------------------------------------- |
| `$requestExecutor` | `callable`                          |                                               |
| `$request`         | `Psr\Http\Message\RequestInterface` | The original HTTP request for context         |
| `$endpoint`        | `string`                            | The endpoint URL for circuit breaker tracking |

#### Returns

`Psr\Http\Message\ResponseInterface` — The successful HTTP response
