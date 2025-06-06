<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\AuthorizationModelsInterface;
use OpenFGA\Schemas\SchemaInterface;

/**
 * Interface for authorization models listing response objects.
 *
 * This interface defines the contract for responses returned when listing authorization
 * models from an OpenFGA store. The response includes a collection of authorization models
 * and pagination support for handling large numbers of models efficiently.
 *
 * Authorization model listing is useful for administrative operations, model versioning
 * management, and allowing users to select from available model versions.
 *
 * @see AuthorizationModelsInterface Collection of authorization model objects
 * @see https://openfga.dev/api/service OpenFGA List Authorization Models API Documentation
 */
interface ListAuthorizationModelsResponseInterface extends ResponseInterface
{
    /**
     * Get the schema definition for this response.
     *
     * Returns the schema that defines the structure and validation rules for authorization
     * models listing response data, ensuring consistent parsing and validation of API responses.
     *
     * @return SchemaInterface The schema definition for response validation
     */
    public static function schema(): SchemaInterface;

    /**
     * Get the continuation token for pagination.
     *
     * Returns a token that can be used to retrieve the next page of results when
     * the total number of authorization models exceeds the page size limit. If null,
     * there are no more results to fetch.
     *
     * @return string|null The continuation token for fetching more results, or null if no more pages exist
     */
    public function getContinuationToken(): ?string;

    /**
     * Get the collection of authorization models.
     *
     * Returns a type-safe collection containing the authorization model objects from
     * the current page of results. Each model includes its ID, type definitions,
     * schema version, and any conditions.
     *
     * @return AuthorizationModelsInterface The collection of authorization models
     */
    public function getModels(): AuthorizationModelsInterface;
}
