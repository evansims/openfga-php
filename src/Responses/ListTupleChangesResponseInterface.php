<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\TupleChangesInterface;
use OpenFGA\Schemas\SchemaInterface;

/**
 * Interface for tuple changes listing response objects.
 *
 * This interface defines the contract for responses returned when listing changes
 * to relationship tuples in OpenFGA. The response includes a collection of tuple
 * changes and pagination support for handling large change sets efficiently.
 *
 * Tuple change listing is essential for auditing authorization modifications,
 * implementing change feeds, and tracking the evolution of relationship data
 * over time.
 *
 * @see TupleChangesInterface Collection of tuple change objects
 * @see https://openfga.dev/docs/interacting/read-tuple-changes OpenFGA List Tuple Changes API Documentation
 */
interface ListTupleChangesResponseInterface extends ResponseInterface
{
    /**
     * Get the schema definition for this response.
     *
     * Returns the schema that defines the structure and validation rules for tuple
     * changes listing response data, ensuring consistent parsing and validation of API responses.
     *
     * @return SchemaInterface The schema definition for response validation
     */
    public static function schema(): SchemaInterface;

    /**
     * Get the collection of tuple changes.
     *
     * Returns a type-safe collection containing the tuple change objects from the
     * current page of results. Each change represents a modification (insert or delete)
     * to the relationship data, including timestamps and operation details.
     *
     * @return TupleChangesInterface The collection of tuple changes
     */
    public function getChanges(): TupleChangesInterface;

    /**
     * Get the continuation token for pagination.
     *
     * Returns a token that can be used to retrieve the next page of results when
     * the total number of tuple changes exceeds the page size limit. If null,
     * there are no more results to fetch.
     *
     * @return string|null The continuation token for fetching more results, or null if no more pages exist
     */
    public function getContinuationToken(): ?string;
}
