<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use Throwable;

/**
 * Interface for concurrent task execution.
 *
 * This interface defines the contract for executing multiple tasks
 * concurrently, providing improved performance for batch operations.
 * Implementations can use different concurrency strategies such as
 * Fibers, threads, or process pools.
 */
interface ConcurrentExecutorInterface
{
    /**
     * Execute multiple tasks in parallel.
     *
     * Executes the provided tasks concurrently up to the specified
     * concurrency limit. Tasks are executed as they become available
     * and results are collected in the same order as the input tasks.
     *
     * @template T
     *
     * @param array<int, callable(): T> $tasks         Array of callables to execute
     * @param int                       $maxConcurrent Maximum number of concurrent executions
     *
     * @throws Throwable If any task fails and error handling is not configured
     *
     * @return array<int, T|Throwable> Array of results or exceptions in the same order as tasks
     */
    public function executeParallel(array $tasks, int $maxConcurrent = 10): array;

    /**
     * Get the maximum recommended concurrency for the current environment.
     *
     * This provides a hint about the optimal concurrency level based on
     * system resources and implementation constraints.
     *
     * @return int Maximum recommended concurrent tasks
     */
    public function getMaxRecommendedConcurrency(): int;

    /**
     * Check if the executor supports concurrent execution.
     *
     * Some environments may not support true concurrency (e.g., missing
     * Fiber support in PHP < 8.1). This method allows checking if the
     * executor can actually run tasks concurrently or will fall back
     * to sequential execution.
     *
     * @return bool True if concurrent execution is supported
     */
    public function supportsConcurrency(): bool;
}
