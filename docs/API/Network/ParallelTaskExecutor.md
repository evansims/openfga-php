# ParallelTaskExecutor

Executes tasks in parallel using the RequestManager infrastructure. This class provides a clean abstraction for parallel task execution, leveraging the existing Fiber-based implementation in RequestManager.

## Namespace

`OpenFGA\Network`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Network/ParallelTaskExecutor.php)

## Methods

#### execute

```php
public function execute(
    array<callable(): (FailureInterface|SuccessInterface)> $tasks,
    int $maxParallelRequests,
    bool $stopOnFirstError,
): array<FailureInterface|SuccessInterface>

```

Execute tasks with specified parallelism.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Network/ParallelTaskExecutor.php#L34)

#### Parameters

| Name                   | Type                                                                    | Description                    |
| ---------------------- | ----------------------------------------------------------------------- | ------------------------------ |
| `$tasks`               | `array&lt;callable(): (FailureInterface` &#124; `SuccessInterface)&gt;` |                                |
| `$maxParallelRequests` | `int`                                                                   | Maximum concurrent requests    |
| `$stopOnFirstError`    | `bool`                                                                  | Whether to stop on first error |

#### Returns

`array&lt;FailureInterface` &#124; `SuccessInterface&gt;` — Results from each task
