<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

/**
 * Interface for retrieving information about an OpenFGA store.
 *
 * This interface defines the contract for requests that fetch metadata
 * and configuration information for a specific OpenFGA store. Store
 * information includes details such as the store name, creation time,
 * and other administrative metadata.
 *
 * Getting store information is useful for:
 * - Administrative interfaces and dashboards
 * - Verifying store existence before performing operations
 * - Displaying store metadata to users
 * - Auditing and monitoring store usage
 * - Implementing store management workflows
 *
 * @see https://openfga.dev/docs/api/service#Stores/GetStore OpenFGA Get Store API Documentation
 * @see https://openfga.dev/docs/concepts#what-is-a-store OpenFGA Store Concepts
 */
interface GetStoreRequestInterface extends RequestInterface
{
    /**
     * Get the ID of the store to retrieve.
     *
     * Returns the unique identifier of the store whose information should
     * be fetched. This will return metadata about the store including its
     * name, creation timestamp, and other administrative details.
     *
     * @return string The unique identifier of the store to retrieve information for
     */
    public function getStore(): string;
}
