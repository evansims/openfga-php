<?php

declare(strict_types=1);

namespace OpenFGA\Repositories;

use OpenFGA\Results\ResultInterface;

/**
 * Repository interface for store operations.
 *
 * This interface defines the contract for store repository implementations,
 * providing a domain-focused abstraction for store management operations.
 * All methods follow the Result pattern, returning either Success or Failure
 * objects to enable safe error handling without exceptions for control flow.
 *
 * Implementations should handle all infrastructure concerns such as HTTP
 * communication, data persistence, or caching while presenting a clean
 * domain interface to the application layer.
 *
 * @see https://openfga.dev/docs/api#/Stores Store management documentation
 */
interface StoreRepositoryInterface
{
    /**
     * Create a new store with the specified name.
     *
     * Creates a new OpenFGA store which serves as a container for authorization
     * models and relationship tuples. Each store is isolated from others, allowing
     * you to manage multiple authorization configurations in a single OpenFGA instance.
     *
     * @param  string          $name The name for the new store
     * @return ResultInterface Success containing the created Store, or Failure with error details
     */
    public function create(string $name): ResultInterface;

    /**
     * Delete an existing store by ID.
     *
     * Permanently removes a store and all its associated data including authorization
     * models and relationship tuples. This operation cannot be undone, so use with
     * caution in production environments.
     *
     * @param  string          $storeId The ID of the store to delete
     * @return ResultInterface Success with null value, or Failure with error details
     */
    public function delete(string $storeId): ResultInterface;

    /**
     * Get a store by ID.
     *
     * Retrieves the details of an existing store including its name and timestamps.
     * Use this to verify a store exists or to get its current metadata.
     *
     * @param  string          $storeId The ID of the store to retrieve
     * @return ResultInterface Success containing the Store, or Failure with error details
     */
    public function get(string $storeId): ResultInterface;

    /**
     * List available stores with optional pagination.
     *
     * Retrieves a paginated list of all stores accessible to the authenticated client.
     * Use the continuation token from a previous response to fetch subsequent pages
     * when dealing with large numbers of stores.
     *
     * @param  string|null     $continuationToken Token from previous response to get next page
     * @param  int|null        $pageSize          Maximum number of stores to return (1-100)
     * @return ResultInterface Success containing Stores collection, or Failure with error details
     */
    public function list(?string $continuationToken = null, ?int $pageSize = null): ResultInterface;
}
