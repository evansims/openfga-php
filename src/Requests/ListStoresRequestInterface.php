<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

/**
 * Interface for listing available OpenFGA stores.
 *
 * This interface defines the contract for requests that retrieve a paginated
 * list of all OpenFGA stores accessible to the current authentication context.
 * This is typically used for administrative purposes, allowing users to browse
 * and manage multiple authorization domains.
 *
 * Store listing is essential for:
 * - Administrative dashboards and management interfaces
 * - Store discovery and selection workflows
 * - Monitoring and auditing store usage across an organization
 * - Implementing multi-tenant authorization architectures
 * - Backup and migration tooling that needs to enumerate stores
 *
 * The operation supports pagination to handle large numbers of stores
 * efficiently, ensuring good performance even in environments with
 * hundreds or thousands of authorization domains.
 *
 * @see https://openfga.dev/docs/api/service#Stores/ListStores OpenFGA List Stores API Documentation
 * @see https://openfga.dev/docs/concepts#what-is-a-store OpenFGA Store Concepts
 */
interface ListStoresRequestInterface extends RequestInterface
{
    /**
     * Get the continuation token for paginated results.
     *
     * Returns the pagination token from a previous list stores operation to
     * continue retrieving results from where the last request left off. This
     * enables efficient pagination through large numbers of stores without
     * missing or duplicating entries.
     *
     * @return string|null The continuation token from a previous operation, or null for the first page
     */
    public function getContinuationToken(): ?string;

    /**
     * Get the maximum number of stores to return per page.
     *
     * Specifies the page size for paginated results. This controls how many
     * stores are returned in a single response. Smaller page sizes reduce
     * memory usage and latency, while larger page sizes reduce the number
     * of API calls needed to retrieve all stores.
     *
     * @return int|null The maximum number of stores to return per page, or null to use the default page size
     */
    public function getPageSize(): ?int;
}
