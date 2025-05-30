<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;

/**
 * Interface for reading relationship tuples from an OpenFGA store.
 *
 * This interface defines the contract for requests that query relationship
 * tuples stored in OpenFGA. It supports filtering by tuple patterns and
 * provides pagination for handling large result sets efficiently.
 *
 * Read operations allow you to:
 * - Query existing relationships using tuple key patterns
 * - Filter by specific objects, users, or relations
 * - Use wildcard patterns to match multiple tuples
 * - Configure read consistency for performance optimization
 * - Paginate through large result sets with continuation tokens
 *
 * This is essential for auditing permissions, syncing data to external
 * systems, building administrative interfaces, and implementing custom
 * authorization logic that needs to inspect the relationship graph.
 *
 * @see Consistency Read consistency options for balancing performance and accuracy
 * @see TupleKeyInterface Individual relationship tuple structure for filtering
 * @see https://openfga.dev/docs/api/service#Relationship%20Tuples/Read OpenFGA Read Tuples API Documentation
 */
interface ReadTuplesRequestInterface extends RequestInterface
{
    /**
     * Get the read consistency level for the read operation.
     *
     * Determines the consistency guarantees for reading relationship tuples.
     * This allows you to balance between read performance and data freshness
     * based on your application's requirements.
     *
     * @return Consistency|null The consistency level for the operation, or null to use the default consistency setting
     */
    public function getConsistency(): ?Consistency;

    /**
     * Get the continuation token for paginated results.
     *
     * Returns the pagination token from a previous read operation to continue
     * retrieving results from where the last request left off. This enables
     * efficient pagination through large result sets without missing or
     * duplicating tuples.
     *
     * @return string|null The continuation token from a previous read operation, or null for the first page
     */
    public function getContinuationToken(): ?string;

    /**
     * Get the maximum number of tuples to return.
     *
     * Specifies the page size for paginated results. This controls how many
     * relationship tuples are returned in a single response. Smaller page sizes
     * reduce memory usage and latency, while larger page sizes reduce the number
     * of API calls needed for large datasets.
     *
     * @return int|null The maximum number of tuples to return per page, or null to use the default page size
     */
    public function getPageSize(): ?int;

    /**
     * Get the store ID containing the tuples to read.
     *
     * Identifies which OpenFGA store contains the relationship tuples to query.
     * All read operations will be performed within the context of this specific
     * store, ensuring data isolation from other stores.
     *
     * @return string The store ID containing the relationship tuples to read
     */
    public function getStore(): string;

    /**
     * Get the tuple key pattern for filtering results.
     *
     * Specifies the relationship pattern to match when reading tuples. This
     * can include specific values for object, user, and relation, or use
     * partial patterns with wildcards to match multiple tuples. Empty or
     * null values in the tuple key act as wildcards.
     *
     * @return TupleKeyInterface The relationship tuple pattern for filtering results
     */
    public function getTupleKey(): TupleKeyInterface;
}
