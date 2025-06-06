<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use OpenFGA\Models\Collections\AssertionsInterface;
use OpenFGA\Models\StoreInterface;
use OpenFGA\Results\{FailureInterface, SuccessInterface};

/**
 * Service interface for managing OpenFGA authorization model assertions.
 *
 * This service provides business-focused operations for working with assertions,
 * which are test cases that validate the behavior of authorization models.
 * Assertions help ensure that your authorization model works as expected by
 * defining specific scenarios and their expected outcomes.
 *
 * ## Core Operations
 *
 * The service supports assertion management with enhanced functionality:
 * - Read existing assertions from authorization models
 * - Write new assertions to validate model behavior
 * - Validate assertion syntax and logic
 * - Batch operations for managing multiple assertions
 *
 * ## Assertion Validation
 *
 * Assertions define test cases like:
 * - "user:anne should have reader access to document:budget-2024"
 * - "user:bob should NOT have admin access to folder:public"
 * - "group:finance#member should have write access to report:quarterly"
 *
 * ## Usage Example
 *
 * ```php
 * $assertionService = new AssertionService($assertionRepository);
 *
 * // Read existing assertions
 * $assertions = $assertionService->readAssertions(
 *     $store,
 *     $authorizationModel
 * )->unwrap();
 *
 * // Write new assertions
 * $newAssertions = new Assertions([
 *     new Assertion(
 *         new TupleKey('user:anne', 'reader', 'document:budget'),
 *         true // expected result
 *     )
 * ]);
 *
 * $result = $assertionService->writeAssertions(
 *     $store,
 *     $authorizationModel,
 *     $newAssertions
 * )->unwrap();
 * ```
 *
 * @see AssertionInterface Individual assertion representation
 * @see AuthorizationModelInterface Model containing assertions
 */
interface AssertionServiceInterface
{
    /**
     * Clear all assertions from an authorization model.
     *
     * Removes all test assertions from the specified authorization model.
     * This is useful when completely restructuring test cases or during
     * development iterations.
     *
     * @param  StoreInterface|string             $store                The store containing the model
     * @param  string                            $authorizationModelId The authorization model to clear
     * @return FailureInterface|SuccessInterface Success if cleared, or Failure with error details
     */
    public function clearAssertions(
        StoreInterface | string $store,
        string $authorizationModelId,
    ): FailureInterface | SuccessInterface;

    /**
     * Execute assertions against the authorization model.
     *
     * Runs the specified assertions and returns the results, comparing
     * expected outcomes with actual authorization check results. This
     * helps verify that your authorization model works correctly.
     *
     * @param  StoreInterface|string             $store                The store to execute assertions against
     * @param  string                            $authorizationModelId The authorization model to test
     * @param  AssertionsInterface               $assertions           The assertions to execute
     * @return FailureInterface|SuccessInterface Success with test results, or Failure with execution errors
     */
    public function executeAssertions(
        StoreInterface | string $store,
        string $authorizationModelId,
        AssertionsInterface $assertions,
    ): FailureInterface | SuccessInterface;

    /**
     * Get assertion execution statistics.
     *
     * Provides insights into assertion test results, including pass/fail counts,
     * execution times, and common failure patterns. Useful for monitoring
     * authorization model health and test coverage.
     *
     * @param  StoreInterface|string             $store                The store to analyze
     * @param  string                            $authorizationModelId The authorization model to analyze
     * @return FailureInterface|SuccessInterface Success with statistics, or Failure with error details
     */
    public function getAssertionStatistics(
        StoreInterface | string $store,
        string $authorizationModelId,
    ): FailureInterface | SuccessInterface;

    /**
     * Read assertions from an authorization model.
     *
     * Retrieves all test assertions defined in the specified authorization model.
     * Assertions validate that the model behaves correctly for specific scenarios.
     *
     * @param  StoreInterface|string             $store                The store containing the model
     * @param  string                            $authorizationModelId The authorization model ID containing assertions
     * @return FailureInterface|SuccessInterface Success with assertions collection, or Failure with error details
     */
    public function readAssertions(
        StoreInterface | string $store,
        string $authorizationModelId,
    ): FailureInterface | SuccessInterface;

    /**
     * Validate assertion syntax and logic.
     *
     * Checks that assertions are properly formatted and reference valid
     * types and relations from the authorization model. This helps catch
     * errors before deploying assertions to production.
     *
     * @param  AssertionsInterface               $assertions           The assertions to validate
     * @param  string                            $authorizationModelId The authorization model to validate against
     * @return FailureInterface|SuccessInterface Success if valid, or Failure with validation errors
     */
    public function validateAssertions(
        AssertionsInterface $assertions,
        string $authorizationModelId,
    ): FailureInterface | SuccessInterface;

    /**
     * Write assertions to an authorization model.
     *
     * Updates the test assertions for the specified authorization model.
     * Assertions help validate that your authorization model works as expected
     * by defining specific test cases and their expected outcomes.
     *
     * @param  StoreInterface|string             $store                The store containing the model
     * @param  string                            $authorizationModelId The authorization model ID to update
     * @param  AssertionsInterface               $assertions           The assertions to write
     * @return FailureInterface|SuccessInterface Success if written, or Failure with error details
     */
    public function writeAssertions(
        StoreInterface | string $store,
        string $authorizationModelId,
        AssertionsInterface $assertions,
    ): FailureInterface | SuccessInterface;
}
