# CircuitBreaker

Circuit breaker implementation for preventing cascade failures in distributed systems. This class implements the circuit breaker pattern to temporarily disable requests to failing endpoints, preventing resource exhaustion and allowing time for recovery. The circuit breaker tracks failures per endpoint and automatically opens/closes based on failure thresholds and cooldown periods. The circuit breaker operates in three states: - Closed: Normal operation, requests are allowed - Open: Failures exceeded threshold, requests are blocked - Half-Open: After cooldown, limited requests allowed to test recovery

## Namespace
`OpenFGA\Network`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/CircuitBreaker.php)

## Implements
* [`CircuitBreakerInterface`](CircuitBreakerInterface.md)

## Related Classes
* [CircuitBreakerInterface](Network/CircuitBreakerInterface.md) (interface)

## Methods

### List Operations
#### getFailureCount

```php
public function getFailureCount(string $endpoint): int
```

Get the current failure count for an endpoint. Returns the number of consecutive failures recorded for the specified endpoint. This can be useful for logging and monitoring purposes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/CircuitBreaker.php#L54)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$endpoint` | `string` | The endpoint URL or identifier to check |

#### Returns
`int` — The current failure count (0 if no failures recorded)
### Utility
#### isOpen

```php
public function isOpen(string $endpoint): bool
```

Check if the circuit is currently open for an endpoint. Returns true if the circuit breaker is currently blocking requests to the specified endpoint due to excessive failures.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/CircuitBreaker.php#L65)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$endpoint` | `string` | The endpoint URL or identifier to check |

#### Returns
`bool` — True if the circuit is open (blocking requests), false otherwise
### Other
#### recordFailure

```php
public function recordFailure(string $endpoint): void
```

Record a failure for the specified endpoint. Increments the failure count for the endpoint and updates the failure timestamp. If the failure threshold is reached, the circuit will open and block subsequent requests until the cooldown period expires.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/CircuitBreaker.php#L74)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$endpoint` | `string` | The endpoint URL or identifier that failed |

#### Returns
`void`
#### recordSuccess

```php
public function recordSuccess(string $endpoint): void
```

Record a successful request for the specified endpoint. Resets the failure state for the endpoint, effectively closing the circuit and allowing normal operation to resume. This should be called whenever a request succeeds after previous failures.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/CircuitBreaker.php#L85)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$endpoint` | `string` | The endpoint URL or identifier that succeeded |

#### Returns
`void`
#### shouldRetry

```php
public function shouldRetry(string $endpoint): bool
```

Check if the circuit breaker should allow a request to the specified endpoint. Evaluates whether a request should be allowed based on the current circuit state for the given endpoint. If the cooldown period has passed, the circuit is automatically reset to allow new attempts.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/CircuitBreaker.php#L95)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$endpoint` | `string` | The endpoint URL or identifier to check |

#### Returns
`bool` — True if requests should be allowed, false if the circuit is open
