<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\AssertionsInterface;
use OpenFGA\Schemas\SchemaInterface;

/**
 * Interface for assertions reading response objects.
 *
 * This interface defines the contract for responses returned when reading assertions
 * from an OpenFGA authorization model. Assertions are test cases that validate the
 * behavior of an authorization model by specifying expected permission check results.
 *
 * Assertion reading is used for testing authorization models, validating model
 * behavior, and ensuring that permission logic works as expected during development
 * and deployment.
 *
 * @see AssertionsInterface Collection of assertion objects
 * @see https://openfga.dev/api/service OpenFGA Read Assertions API Documentation
 */
interface ReadAssertionsResponseInterface extends ResponseInterface
{
    /**
     * Get the schema definition for this response.
     *
     * Returns the schema that defines the structure and validation rules for assertions
     * reading response data, ensuring consistent parsing and validation of API responses.
     *
     * @return SchemaInterface The schema definition for response validation
     */
    public static function schema(): SchemaInterface;

    /**
     * Get the collection of assertions from the authorization model.
     *
     * Returns a type-safe collection containing the assertion objects associated
     * with the authorization model. Each assertion defines a test case with expected
     * permission check results for validating model behavior.
     *
     * @return AssertionsInterface|null The collection of assertions, or null if no assertions are defined
     */
    public function getAssertions(): ?AssertionsInterface;

    /**
     * Get the authorization model identifier for these assertions.
     *
     * Returns the unique identifier of the authorization model that contains
     * these assertions. This ties the assertions to a specific model version
     * for validation and testing purposes.
     *
     * @return string The authorization model identifier
     */
    public function getModel(): string;
}
