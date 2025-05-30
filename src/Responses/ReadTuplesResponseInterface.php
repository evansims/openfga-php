<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\TuplesInterface;
use OpenFGA\Models\TupleInterface;
use OpenFGA\Schema\SchemaInterface;

/**
 * Interface for tuple reading response objects.
 *
 * This interface defines the contract for responses returned when reading relationship
 * tuples from OpenFGA. The response includes a collection of tuples matching the query
 * criteria and pagination support for handling large result sets efficiently.
 *
 * Tuple reading is essential for querying existing relationships, auditing authorization
 * data, and implementing administrative interfaces for relationship management.
 *
 * @see TuplesInterface Collection of tuple objects
 * @see https://openfga.dev/docs/interacting/managing-relationships-between-objects OpenFGA Read Tuples API Documentation
 */
interface ReadTuplesResponseInterface extends ResponseInterface
{
    /**
     * Get the schema definition for this response.
     *
     * Returns the schema that defines the structure and validation rules for tuple
     * reading response data, ensuring consistent parsing and validation of API responses.
     *
     * @return SchemaInterface The schema definition for response validation
     */
    public static function schema(): SchemaInterface;

    /**
     * Get the continuation token for pagination.
     *
     * Returns a token that can be used to retrieve the next page of results when
     * the total number of matching tuples exceeds the page size limit. If null,
     * there are no more results to fetch.
     *
     * @return string|null The continuation token for fetching more results, or null if no more pages exist
     */
    public function getContinuationToken(): ?string;

    /**
     * Get the collection of relationship tuples.
     *
     * Returns a type-safe collection containing the tuple objects that match the
     * read query criteria. Each tuple represents a relationship between a user
     * and an object through a specific relation.
     *
     * @return TuplesInterface<TupleInterface> The collection of relationship tuples
     */
    public function getTuples(): TuplesInterface;
}
