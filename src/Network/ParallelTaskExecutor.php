<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use OpenFGA\Results\{FailureInterface, SuccessInterface};

/**
 * Executes tasks in parallel using the RequestManager infrastructure.
 *
 * This class provides a clean abstraction for parallel task execution,
 * leveraging the existing Fiber-based implementation in RequestManager.
 */
final readonly class ParallelTaskExecutor
{
    public function __construct(
        private RequestManagerFactory $requestManagerFactory,
    ) {
    }

    /**
     * Execute tasks with specified parallelism.
     *
     * @param  array<callable(): (FailureInterface|SuccessInterface)> $tasks               Array of callable tasks
     * @param  int                                                    $maxParallelRequests Maximum concurrent requests
     * @param  bool                                                   $stopOnFirstError    Whether to stop on first error
     * @return array<FailureInterface|SuccessInterface>               Results from each task
     */
    public function execute(array $tasks, int $maxParallelRequests, bool $stopOnFirstError): array
    {
        $requestManager = $this->requestManagerFactory->createForBatch();

        return $requestManager->executeParallel($tasks, $maxParallelRequests, $stopOnFirstError);
    }
}
