# RetryHandler

Advanced retry handler with exponential backoff, jitter, and circuit breaker integration. This class implements a sophisticated retry strategy for HTTP requests, providing: - Exponential backoff with configurable jitter to prevent thundering herd - Respect for server-provided retry timing via Retry-After and rate limit headers - Circuit breaker integration to prevent cascade failures - Context-aware retry limits based on operation type - Comprehensive logging and monitoring support The retry handler categorizes errors and applies appropriate retry strategies: - Network errors: Fast initial retry with exponential backoff - Rate limits (429): Honor server timing headers exactly - Server errors (5xx): Standard exponential backoff - Maintenance (503): Extended delays for service recovery

## Namespace
`OpenFGA\Network`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/RetryHandler.php)

## Implements
* [RetryHandlerInterface](RetryHandlerInterface.md)

## Related Classes
* [RetryHandlerInterface](Network/RetryHandlerInterface.md) (interface)



## Methods

                        
#### executeWithRetry


```php
public function executeWithRetry(callable $requestExecutor, Psr\Http\Message\RequestInterface $request, string $endpoint): Psr\Http\Message\ResponseInterface
```

Execute an HTTP request with automatic retry logic. Performs the HTTP request with intelligent retry behavior based on error type, server headers, and circuit breaker state. The method tracks attempt counts, calculates appropriate delays, and respects server-provided timing information. The implementation should: - Check circuit breaker state before attempting requests - Apply exponential backoff with jitter to prevent thundering herd - Respect server-provided timing headers (Retry-After, X-Rate-Limit-Reset) - Handle different error types with appropriate retry strategies - Consider request method idempotency for retry decisions - Track failures and successes with the circuit breaker

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RetryHandler.php#L102)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$requestExecutor` | callable |  |
| `$request` | Psr\Http\Message\RequestInterface | The original HTTP request for context |
| `$endpoint` | string | The endpoint URL for circuit breaker tracking |

#### Returns
Psr\Http\Message\ResponseInterface
 The successful HTTP response

