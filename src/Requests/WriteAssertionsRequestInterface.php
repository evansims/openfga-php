<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{AssertionInterface, Collections\AssertionsInterface};

/**
 * Interface for writing test assertions to an authorization model.
 *
 * This interface defines the contract for requests that create or update
 * test assertions for authorization models in OpenFGA. Assertions are
 * automated tests that verify your authorization model behaves as expected
 * by checking specific permission scenarios against known outcomes.
 *
 * Assertions serve multiple important purposes:
 * - **Testing**: Verify that your authorization model produces expected results
 * - **Validation**: Ensure model changes don't break existing authorization logic
 * - **Documentation**: Provide examples of how permissions should work
 * - **Regression Prevention**: Catch unintended changes to authorization behavior
 * - **Continuous Integration**: Enable automated testing of authorization logic
 *
 * Each assertion defines:
 * - A specific permission check scenario (user, object, relation)
 * - The expected outcome (allowed or denied)
 * - Optional contextual data for conditional authorization
 *
 * Assertions are tied to specific authorization model versions, allowing you
 * to maintain test suites that evolve with your authorization schema. When
 * you create a new model version, you can run existing assertions to ensure
 * backward compatibility or create new assertions for new functionality.
 *
 * This is essential for maintaining confidence in your authorization system
 * as it evolves, especially in complex scenarios with inheritance, conditions,
 * and computed relationships.
 *
 * @see AssertionInterface Individual test assertion
 * @see AssertionsInterface Collection of test assertions
 * @see https://openfga.dev/docs/api/service#Assertions/WriteAssertions OpenFGA Write Assertions API Documentation
 * @see https://openfga.dev/docs/modeling/testing-models OpenFGA Model Testing Guide
 */
interface WriteAssertionsRequestInterface extends RequestInterface
{
    /**
     * Get the test assertions to write to the authorization model.
     *
     * Returns a collection of assertions that define test scenarios for the
     * authorization model. Each assertion specifies a permission check and
     * its expected outcome, creating a comprehensive test suite that verifies
     * the model's behavior across various scenarios.
     *
     * Assertions help ensure that:
     * - Permission checks return expected results
     * - Model changes don't introduce regressions
     * - Complex authorization logic works correctly
     * - Edge cases and special scenarios are properly handled
     * - Documentation of expected behavior is maintained
     *
     * @return AssertionsInterface Collection of test assertions to validate authorization model behavior
     */
    public function getAssertions(): AssertionsInterface;

    /**
     * Get the authorization model ID to associate assertions with.
     *
     * Specifies which version of the authorization model these assertions
     * should be tied to. Assertions are version-specific, allowing you to
     * maintain different test suites for different model versions and ensure
     * that tests remain relevant as your authorization schema evolves.
     *
     * @return string The authorization model ID that these assertions will test
     */
    public function getModel(): string;

    /**
     * Get the store ID where assertions will be written.
     *
     * Identifies the OpenFGA store that contains the authorization model
     * and where the test assertions will be stored. Assertions are stored
     * alongside the model they test, providing a complete testing framework
     * within each store.
     *
     * @return string The store ID where the test assertions will be written
     */
    public function getStore(): string;
}
