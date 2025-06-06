<?php

declare(strict_types=1);

namespace OpenFGA\Network;

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
    public function executeParallel(array $tasks, int $maxConcurrent = 10): array
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
