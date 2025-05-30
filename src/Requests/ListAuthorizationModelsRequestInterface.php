<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

/**
 * Interface for listing authorization models in a store.
 *
 * This interface defines the contract for requests that retrieve a paginated
 * list of all authorization model versions within a specific OpenFGA store.
 * Authorization models are versioned, and this operation allows you to browse
 * through the evolution of your authorization schema over time.
 *
 * Listing authorization models is useful for:
 * - Administrative interfaces showing model version history
 * - Implementing model rollback and comparison functionality
 * - Auditing changes to authorization schemas over time
 * - Building deployment and migration tools for authorization models
 * - Understanding the evolution of permission structures
 * - Debugging authorization issues by examining model versions
 *
 * Each model in the list includes metadata such as creation time and model ID,
 * allowing you to understand when changes were made and select specific
 * versions for detailed inspection or operational use.
 *
 * @see https://openfga.dev/docs/api/service#Authorization%20Models/ReadAuthorizationModels OpenFGA List Authorization Models API Documentation
 * @see https://openfga.dev/docs/modeling OpenFGA Authorization Modeling Guide
 */
interface ListAuthorizationModelsRequestInterface extends RequestInterface
{
    /**
     * Get the continuation token for paginated results.
     *
     * Returns the pagination token from a previous list models operation to
     * continue retrieving results from where the last request left off. This
     * enables efficient pagination through stores with many model versions
     * without missing or duplicating entries.
     *
     * @return string|null The continuation token from a previous operation, or null for the first page
     */
    public function getContinuationToken(): ?string;

    /**
     * Get the maximum number of models to return per page.
     *
     * Specifies the page size for paginated results. This controls how many
     * authorization models are returned in a single response. Smaller page
     * sizes reduce memory usage and latency, while larger page sizes reduce
     * the number of API calls needed to retrieve all model versions.
     *
     * @return int|null The maximum number of models to return per page, or null to use the default page size
     */
    public function getPageSize(): ?int;

    /**
     * Get the store ID containing the authorization models to list.
     *
     * Identifies which OpenFGA store contains the authorization models to
     * enumerate. Each store maintains its own independent collection of
     * model versions, representing the evolution of that store's authorization
     * schema over time.
     *
     * @return string The store ID containing the authorization models to list
     */
    public function getStore(): string;
}
