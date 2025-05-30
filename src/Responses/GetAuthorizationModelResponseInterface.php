<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\AuthorizationModelInterface;
use OpenFGA\Schema\SchemaInterface;

/**
 * Interface for authorization model retrieval response objects.
 *
 * This interface defines the contract for responses returned when retrieving authorization
 * models from OpenFGA. An authorization model defines the relationship types, object types,
 * and permission logic that govern how authorization decisions are made within a store.
 *
 * Authorization models are versioned, allowing you to evolve your permission structure
 * over time while maintaining consistency for existing authorization checks.
 *
 * @see AuthorizationModelInterface The authorization model structure
 * @see https://openfga.dev/api/service OpenFGA Get Authorization Model API Documentation
 */
interface GetAuthorizationModelResponseInterface extends ResponseInterface
{
    /**
     * Get the schema definition for this response.
     *
     * Returns the schema that defines the structure and validation rules for authorization
     * model response data, ensuring consistent parsing and validation of API responses.
     *
     * @return SchemaInterface The schema definition for response validation
     */
    public static function schema(): SchemaInterface;

    /**
     * Get the retrieved authorization model.
     *
     * Returns the complete authorization model including its type definitions, schema version,
     * and any conditions. The model defines the relationship types and permission logic
     * that govern authorization decisions within the store.
     *
     * @return AuthorizationModelInterface|null The authorization model, or null if not found
     */
    public function getModel(): ?AuthorizationModelInterface;
}
