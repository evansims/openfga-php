<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use OpenFGA\Results\FailureInterface;
use Override;
use Throwable;

/**
 * Simple concurrent executor implementation.
 *
 * This implementation provides a fallback for environments without
 * Fiber support. It executes tasks sequentially while maintaining
 * the same interface as the fiber-based implementation.
 */
final class SimpleConcurrentExecutor implements ConcurrentExecutorInterface
{
    /**
     * @inheritDoc
     */
    #[Override]
    public function executeParallel(array $tasks, int $maxConcurrent = 10, bool $stopOnFirstError = false): array
    {
        $results = [];

        foreach ($tasks as $index => $task) {
            try {
                $results[$index] = $task();

                // Stop on returned Failure as well
                if ($stopOnFirstError && $results[$index] instanceof FailureInterface) {
                    break;
                }
            } catch (Throwable $e) {
                $results[$index] = $e;

                if ($stopOnFirstError) {
                    break;
                }
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
        return 1; // Sequential execution only
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function supportsConcurrency(): bool
    {
        return false; // This implementation doesn't support true concurrency
    }
}
