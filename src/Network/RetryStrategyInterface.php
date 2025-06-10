<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use Throwable;

/**
 * Interface for implementing retry strategies.
 *
 * This interface defines the contract for different retry strategies
 * that can be used when operations fail. Implementations can provide
 * various retry algorithms such as exponential backoff, linear retry,
 * or custom strategies based on specific requirements.
 */
interface RetryStrategyInterface
{
    /**
     * Execute an operation with retry logic.
     *
     * Executes the given operation and retries it according to the
     * strategy's implementation if it fails. The strategy determines
     * when to retry, how long to wait between retries, and when to
     * give up.
     *
     * @template T
     *
     * @param callable(): T        $operation The operation to execute
     * @param array<string, mixed> $config    Optional configuration for the retry strategy
     *
     * @throws Throwable If the operation fails after all retry attempts
     *
     * @return T The result of the successful operation
     */
    public function execute(callable $operation, array $config = []): mixed;

    /**
     * Get the delay before the next retry attempt.
     *
     * @param  int                  $attempt The current attempt number (1-based)
     * @param  array<string, mixed> $config  Optional configuration
     * @return int                  The delay in milliseconds
     */
    public function getRetryDelay(int $attempt, array $config = []): int;

    /**
     * Determine if an exception is retryable.
     *
     * @param  Throwable $exception The exception to check
     * @return bool      True if the operation should be retried, false otherwise
     */
    public function isRetryable(Throwable $exception): bool;
}
