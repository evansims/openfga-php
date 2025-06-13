# RetryStrategyInterface

Interface for implementing retry strategies. This interface defines the contract for different retry strategies that can be used when operations fail. Implementations can provide various retry algorithms such as exponential backoff, linear retry, or custom strategies based on specific requirements.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Methods](#methods)

* [List Operations](#list-operations)
    * [`getRetryDelay()`](#getretrydelay)
* [Utility](#utility)
    * [`isRetryable()`](#isretryable)
* [Other](#other)
    * [`execute()`](#execute)

## Namespace

`OpenFGA\Network`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/RetryStrategyInterface.php)

## Methods

### List Operations

#### getRetryDelay

```php
public function getRetryDelay(int $attempt, array<string, mixed> $config = []): int

```

Get the delay before the next retry attempt.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RetryStrategyInterface.php#L45)

#### Parameters

| Name       | Type                             | Description                          |
| ---------- | -------------------------------- | ------------------------------------ |
| `$attempt` | `int`                            | The current attempt number (1-based) |
| `$config`  | `array&lt;`string`, `mixed`&gt;` |                                      |

#### Returns

`int` — The delay in milliseconds

### Utility

#### isRetryable

```php
public function isRetryable(Throwable $exception): bool

```

Determine if an exception is retryable.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RetryStrategyInterface.php#L53)

#### Parameters

| Name         | Type        | Description            |
| ------------ | ----------- | ---------------------- |
| `$exception` | `Throwable` | The exception to check |

#### Returns

`bool` — True if the operation should be retried, false otherwise

### Other

#### execute

```php
public function execute(callable $operation, array<string, mixed> $config = []): T

```

Execute an operation with retry logic. Executes the given operation and retries it according to the strategy&#039;s implementation if it fails. The strategy determines when to retry, how long to wait between retries, and when to give up.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/RetryStrategyInterface.php#L36)

#### Parameters

| Name         | Type                             | Description |
| ------------ | -------------------------------- | ----------- |
| `$operation` | `callable`                       |             |
| `$config`    | `array&lt;`string`, `mixed`&gt;` |             |

#### Returns

`T` — The result of the successful operation
