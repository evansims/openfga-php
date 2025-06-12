# SimpleConcurrentExecutor

Simple concurrent executor implementation. This implementation provides a fallback for environments without Fiber support. It executes tasks sequentially while maintaining the same interface as the fiber-based implementation.

## Namespace

`OpenFGA\Network`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/SimpleConcurrentExecutor.php)

## Implements

* [`ConcurrentExecutorInterface`](ConcurrentExecutorInterface.md)

## Methods

### List Operations

#### getMaxRecommendedConcurrency

```php
public function getMaxRecommendedConcurrency(): int

```

Get the maximum recommended concurrency for the current environment. This provides a hint about the optimal concurrency level based on system resources and implementation constraints.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/SimpleConcurrentExecutor.php#L42)

#### Returns

`int` — Maximum recommended concurrent tasks

### Other

#### executeParallel

```php
public function executeParallel(array $tasks, int $maxConcurrent = 10): array

```

Execute multiple tasks in parallel. Executes the provided tasks concurrently up to the specified concurrency limit. Tasks are executed as they become available and results are collected in the same order as the input tasks.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/SimpleConcurrentExecutor.php#L23)

#### Parameters

| Name             | Type    | Description                             |
| ---------------- | ------- | --------------------------------------- |
| `$tasks`         | `array` |                                         |
| `$maxConcurrent` | `int`   | Maximum number of concurrent executions |

#### Returns

`array` — Array of results or exceptions in the same order as tasks

#### supportsConcurrency

```php
public function supportsConcurrency(): bool

```

Check if the executor supports concurrent execution. Some environments may not support true concurrency (for example missing Fiber support in PHP &lt; 8.1). This method allows checking if the executor can actually run tasks concurrently or will fall back to sequential execution.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/SimpleConcurrentExecutor.php#L51)

#### Returns

`bool` — True if concurrent execution is supported
