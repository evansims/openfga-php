# ExponentialBackoffRetryStrategy

Exponential backoff retry strategy implementation. This strategy implements exponential backoff with jitter for retrying failed operations. It increases the delay between retries exponentially to reduce load on the server during failure scenarios, while adding random jitter to prevent thundering herd problems.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Methods](#methods)

- [`execute()`](#execute)
  - [`getRetryDelay()`](#getretrydelay)
  - [`isRetryable()`](#isretryable)

</details>

## Namespace

`OpenFGA\Network`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/ExponentialBackoffRetryStrategy.php)

## Implements

- [`RetryStrategyInterface`](RetryStrategyInterface.md)

## Methods

### execute

```php
public function execute(callable $operation, array $config = []): mixed

```

Execute an operation with retry logic. Executes the given operation and retries it according to the strategy&#039;s implementation if it fails. The strategy determines when to retry, how long to wait between retries, and when to give up.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/ExponentialBackoffRetryStrategy.php#L65)

#### Parameters

| Name         | Type       | Description |
| ------------ | ---------- | ----------- |
| `$operation` | `callable` |             |
| `$config`    | `array`    |             |

#### Returns

`mixed` — The result of the successful operation

### getRetryDelay

```php
public function getRetryDelay(int $attempt, array $config = []): int

```

Get the delay before the next retry attempt.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/ExponentialBackoffRetryStrategy.php#L98)

#### Parameters

| Name       | Type    | Description                          |
| ---------- | ------- | ------------------------------------ |
| `$attempt` | `int`   | The current attempt number (1-based) |
| `$config`  | `array` |                                      |

#### Returns

`int` — The delay in milliseconds

### isRetryable

```php
public function isRetryable(Throwable $exception): bool

```

Determine if an exception is retryable.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/ExponentialBackoffRetryStrategy.php#L125)

#### Parameters

| Name         | Type        | Description            |
| ------------ | ----------- | ---------------------- |
| `$exception` | `Throwable` | The exception to check |

#### Returns

`bool` — True if the operation should be retried, false otherwise
