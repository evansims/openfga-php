<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

/**
 * Interface for reading test assertions from an authorization model.
 *
 * This interface defines the contract for requests that retrieve test assertions
 * associated with a specific authorization model. Assertions are automated tests
 * that verify authorization model behavior by checking specific permission
 * scenarios against expected outcomes.
 *
 * Reading assertions is essential for:
 * - **Test Execution**: Running automated tests to verify model behavior
 * - **Model Validation**: Ensuring authorization logic works as expected
 * - **Debugging**: Understanding test scenarios when troubleshooting issues
 * - **Documentation**: Reviewing examples of how permissions should work
 * - **Continuous Integration**: Automating authorization model testing
 * - **Regression Testing**: Verifying that model changes don't break existing behavior
 *
 * The retrieved assertions include the test scenarios, expected outcomes, and
 * any contextual data needed to execute the tests. This provides a complete
 * test suite that can be run to validate the authorization model's correctness.
 *
 * @see https://openfga.dev/docs/api/service#Assertions/ReadAssertions OpenFGA Read Assertions API Documentation
 * @see https://openfga.dev/docs/modeling/testing-models OpenFGA Model Testing Guide
 */
interface ReadAssertionsRequestInterface extends RequestInterface
{
    /**
     * Get the authorization model ID to read assertions from.
     *
     * Specifies which version of the authorization model should have its
     * assertions retrieved. Assertions are tied to specific model versions,
     * ensuring that tests remain relevant to the particular authorization
     * schema they were designed to validate.
     *
     * @return string The authorization model ID whose assertions should be retrieved
     */
    public function getModel(): string;

    /**
     * Get the store ID containing the assertions to read.
     *
     * Identifies which OpenFGA store contains the authorization model and
     * its associated test assertions. Assertions are stored alongside the
     * models they test, providing a complete testing framework within each
     * store's context.
     *
     * @return string The store ID containing the assertions to retrieve
     */
    public function getStore(): string;
}
