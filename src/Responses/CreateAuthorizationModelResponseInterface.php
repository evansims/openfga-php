<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Schemas\SchemaInterface;

/**
 * Interface for authorization model creation response objects.
 *
 * This interface defines the contract for responses returned when creating new authorization
 * models in OpenFGA. An authorization model creation response contains the unique identifier
 * of the newly created model, which can be used for subsequent operations.
 *
 * Authorization models define the relationship types, object types, and permission logic
 * that govern how authorization decisions are made within a store. They are versioned,
 * allowing you to evolve your permission structure over time.
 *
 * @see https://openfga.dev/api/service OpenFGA Create Authorization Model API Documentation
 */
interface CreateAuthorizationModelResponseInterface extends ResponseInterface
{
    /**
     * Get the schema definition for this response.
     *
     * Returns the schema that defines the structure and validation rules for authorization
     * model creation response data, ensuring consistent parsing and validation of API responses.
     *
     * @return SchemaInterface The schema definition for response validation
     */
    public static function schema(): SchemaInterface;

    /**
     * Get the unique identifier of the created authorization model.
     *
     * Returns the system-generated unique identifier for the newly created authorization model.
     * This ID is used in subsequent API operations to reference this specific model version
     * for authorization checks and other operations.
     *
     * @return string The unique authorization model identifier
     */
    public function getModel(): string;
}
