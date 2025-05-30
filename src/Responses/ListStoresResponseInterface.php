<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\StoresInterface;
use OpenFGA\Models\StoreInterface;
use OpenFGA\Schema\SchemaInterface;

/**
 * Interface for stores listing response objects.
 *
 * This interface defines the contract for responses returned when listing authorization
 * stores in OpenFGA. The response includes a collection of stores and pagination support
 * for handling large numbers of stores efficiently.
 *
 * Store listing is useful for administrative operations, allowing you to discover and
 * manage all stores within your OpenFGA instance.
 *
 * @see StoresInterface Collection of store objects
 * @see https://openfga.dev/api/service OpenFGA List Stores API Documentation
 */
interface ListStoresResponseInterface extends ResponseInterface
{
    /**
     * Get the schema definition for this response.
     *
     * Returns the schema that defines the structure and validation rules for store listing
     * response data, ensuring consistent parsing and validation of API responses.
     *
     * @return SchemaInterface The schema definition for response validation
     */
    public static function schema(): SchemaInterface;

    /**
     * Get the continuation token for pagination.
     *
     * Returns a token that can be used to retrieve the next page of results when
     * the total number of stores exceeds the page size limit. If null, there are
     * no more results to fetch.
     *
     * @return string|null The continuation token for fetching more results, or null if no more pages exist
     */
    public function getContinuationToken(): ?string;

    /**
     * Get the collection of stores.
     *
     * Returns a type-safe collection containing the store objects from the current page
     * of results. Each store includes its metadata such as ID, name, and timestamps.
     *
     * @return StoresInterface<StoreInterface> The collection of stores
     */
    public function getStores(): StoresInterface;
}
