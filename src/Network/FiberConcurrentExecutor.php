<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use Fiber;
use Override;
use Throwable;

use function min;

/**
 * Fiber-based concurrent executor implementation.
 *
 * This implementation uses PHP 8.1+ Fibers to execute tasks concurrently
 * without the overhead of threads or processes. Fibers provide cooperative
 * multitasking, allowing efficient concurrent execution of I/O-bound tasks
 * such as HTTP requests.
 */
final class FiberConcurrentExecutor implements ConcurrentExecutorInterface
{
    /**
     * Default maximum concurrent executions.
     */
    private const int DEFAULT_MAX_CONCURRENT = 10;

    /**
     * @inheritDoc
     */
    #[Override]
    public function executeParallel(array $tasks, int $maxConcurrent = self::DEFAULT_MAX_CONCURRENT): array
    {
        if (! $this->supportsConcurrency() || [] === $tasks) {
            return $this->executeSequential($tasks);
        }

        $maxConcurrent = max(1, min($maxConcurrent, $this->getMaxRecommendedConcurrency()));

        /** @var array<int, mixed> $results */
        $results = [];
        $fibers = [];

        // Initialize empty results array - will be populated in order during execution

        // Create all fibers
        foreach ($tasks as $index => $task) {
            $fibers[$index] = $this->createFiber($task);
        }

        // Process fibers in batches
        $chunks = array_chunk($fibers, $maxConcurrent, true);

        foreach ($chunks as $chunk) {
            // Start fibers in this chunk
            foreach ($chunk as $index => $fiber) {
                try {
                    $fiber->start();
                } catch (Throwable $e) {
                    $results[$index] = $e;
                }
            }

            // Wait for all fibers in chunk to complete
            foreach ($chunk as $index => $fiber) {
                if (isset($results[$index])) {
                    continue; // Already failed during start
                }

                $results = $this->addFiberResult($results, $index, $fiber);
            }
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getMaxRecommendedConcurrency(): int
    {
        // Conservative default based on typical HTTP connection limits
        // Can be adjusted based on system resources
        return 50;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function supportsConcurrency(): bool
    {
        return class_exists(Fiber::class);
    }

    /**
     * Add fiber result to results array.
     *
     * @param  array<int, mixed> $results The results array
     * @param  int               $index   The index to store at
     * @param  Fiber             $fiber   The fiber to execute
     * @return array<int, mixed> Updated results array
     *
     * @phpstan-param Fiber<void, void, mixed, void> $fiber
     *
     * @psalm-param Fiber $fiber
     *
     * @psalm-suppress MixedAssignment
     */
    private function addFiberResult(array $results, int $index, Fiber $fiber): array
    {
        $fiberResult = $this->executeFiber($fiber);

        // This assignment is intentionally mixed as per the class design
        $results[$index] = $fiberResult;

        return $results;
    }

    /**
     * Create a fiber for executing a task.
     *
     * @param  callable(): mixed $task The task to execute
     * @return Fiber             The created fiber
     *
     * @phpstan-return Fiber<void, void, mixed, void>
     *
     * @psalm-return Fiber
     *
     * @psalm-suppress TooManyTemplateParams
     */
    private function createFiber(callable $task): Fiber
    {
        /** @var Fiber<void, void, mixed, void> $fiber */
        $fiber = new Fiber(static fn (): mixed => $task());

        return $fiber;
    }

    /**
     * Execute a fiber and return its result.
     *
     * @param  Fiber $fiber The fiber to execute
     * @return mixed The fiber result or exception
     *
     * @phpstan-param Fiber<void, void, mixed, void> $fiber
     *
     * @psalm-param Fiber $fiber
     */
    private function executeFiber(Fiber $fiber): mixed
    {
        try {
            while (! $fiber->isTerminated()) {
                $fiber->resume();
            }

            return $fiber->getReturn();
        } catch (Throwable $throwable) {
            return $throwable;
        }
    }

    /**
     * Execute tasks sequentially as a fallback.
     *
     * @template T
     *
     * @param  array<int, callable(): T> $tasks The tasks to execute
     * @return array<int, T|Throwable>   Array of results or exceptions
     */
    private function executeSequential(array $tasks): array
    {
        $results = [];

        foreach ($tasks as $index => $task) {
            try {
                $results[$index] = $task();
            } catch (Throwable $e) {
                $results[$index] = $e;
            }
        }

        return $results;
    }
}
