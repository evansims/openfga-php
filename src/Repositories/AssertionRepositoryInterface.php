<?php

declare(strict_types=1);

namespace OpenFGA\Repositories;

use OpenFGA\Models\Collections\AssertionsInterface;
use OpenFGA\Results\{FailureInterface, SuccessInterface};

/**
 * Repository interface for managing OpenFGA authorization model assertions.
 *
 * This interface provides data access operations for working with assertions,
 * which are test cases that validate the behavior of authorization models.
 * Implementations handle the underlying storage and retrieval mechanisms.
 *
 * @see AssertionsInterface Collection of assertion models
 */
interface AssertionRepositoryInterface
{
    /**
     * Read assertions from an authorization model.
     *
     * Retrieves all test assertions defined for the specified authorization model.
     * Assertions validate that the model behaves correctly for specific scenarios.
     *
     * @param  string                            $authorizationModelId The authorization model ID containing assertions
     * @return FailureInterface|SuccessInterface Success with assertions collection, or Failure with error details
     */
    public function read(string $authorizationModelId): FailureInterface | SuccessInterface;

    /**
     * Write assertions to an authorization model.
     *
     * Updates the test assertions for the specified authorization model.
     * This replaces any existing assertions with the provided collection.
     *
     * @param  string                            $authorizationModelId The authorization model ID to update
     * @param  AssertionsInterface               $assertions           The assertions to write
     * @return FailureInterface|SuccessInterface Success if written, or Failure with error details
     */
    public function write(string $authorizationModelId, AssertionsInterface $assertions): FailureInterface | SuccessInterface;
}
