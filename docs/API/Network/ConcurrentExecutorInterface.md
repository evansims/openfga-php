# ConcurrentExecutorInterface

Interface for concurrent task execution. This interface defines the contract for executing multiple tasks concurrently, providing improved performance for batch operations. Implementations can use different concurrency strategies such as Fibers, threads, or process pools.

## Namespace

`OpenFGA\Network`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/ConcurrentExecutorInterface.php)

## Methods

### List Operations

#### getMaxRecommendedConcurrency

```php
public function getMaxRecommendedConcurrency(): int

```

Get the maximum recommended concurrency for the current environment. This provides a hint about the optimal concurrency level based on system resources and implementation constraints.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/ConcurrentExecutorInterface.php#L46)

#### Returns

`int` — Maximum recommended concurrent tasks

### Other

#### executeParallel

```php
public function executeParallel(
    array<int, callable(): T> $tasks,
    int $maxConcurrent = 10,
    bool $stopOnFirstError = false,
): array<int, T|Throwable>

```

Execute multiple tasks in parallel. Executes the provided tasks concurrently up to the specified concurrency limit. Tasks are executed as they become available and results are collected in the same order as the input tasks.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/ConcurrentExecutorInterface.php#L36)

#### Parameters

| Name                | Type                                  | Description                                    |
| ------------------- | ------------------------------------- | ---------------------------------------------- |
| `$tasks`            | `array&lt;`int`, `callable(): T`&gt;` |                                                |
| `$maxConcurrent`    | `int`                                 | Maximum number of concurrent executions        |
| `$stopOnFirstError` | `bool`                                | Stop all tasks when first error is encountered |

#### Returns

`array&lt;int, T` &#124; `Throwable&gt;` — Array of results or exceptions in the same order as tasks

#### supportsConcurrency

```php
public function supportsConcurrency(): bool

```

Check if the executor supports concurrent execution. Some environments may not support true concurrency (for example missing Fiber support in PHP &lt; 8.1). This method allows checking if the executor can actually run tasks concurrently or will fall back to sequential execution.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/ConcurrentExecutorInterface.php#L58)

#### Returns

`bool` — True if concurrent execution is supported
