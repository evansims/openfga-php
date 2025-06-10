<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use OpenFGA\Results\{FailureInterface, SuccessInterface};

/**
 * Service interface for high-level store operations.
 *
 * This interface provides a business-focused abstraction over store management,
 * offering convenience methods and enhanced validation beyond basic CRUD operations.
 * It simplifies common store workflows while maintaining the Result pattern for
 * consistent error handling across the SDK.
 *
 * @see https://openfga.dev/docs/api#/Stores Store management documentation
 */
interface StoreServiceInterface
{
    /**
     * Creates a new store with validation.
     *
     * This method creates a new OpenFGA store after validating the provided name.
     * It ensures the name meets requirements before attempting creation, providing
     * clearer error messages than the raw API when validation fails.
     *
     * @param  string                            $name The name for the new store (must not be empty)
     * @return FailureInterface|SuccessInterface Success containing the created Store, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Stores/CreateStore Create Store API reference
     */
    public function createStore(string $name): FailureInterface | SuccessInterface;

    /**
     * Deletes a store with optional confirmation.
     *
     * This method deletes a store after optionally verifying it exists first.
     * When confirmation is enabled, it provides clearer error messages if the
     * store doesn't exist, preventing confusion about failed delete operations.
     *
     * @param  string                            $storeId       The ID of the store to delete
     * @param  bool                              $confirmExists Whether to verify the store exists before deletion
     * @return FailureInterface|SuccessInterface Success with null value, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Stores/DeleteStore Delete Store API reference
     */
    public function deleteStore(string $storeId, bool $confirmExists = true): FailureInterface | SuccessInterface;

    /**
     * Finds a store by ID with enhanced error handling.
     *
     * This method retrieves a store by its ID, providing more descriptive error
     * messages when the store is not found or when other errors occur. It helps
     * distinguish between "not found" and other types of failures.
     *
     * @param  string                            $storeId The ID of the store to find
     * @return FailureInterface|SuccessInterface Success containing the Store, or Failure with detailed error context
     *
     * @see https://openfga.dev/docs/api#/Stores/GetStore Get Store API reference
     */
    public function findStore(string $storeId): FailureInterface | SuccessInterface;

    /**
     * Finds stores by name pattern.
     *
     * This method searches for stores whose names match a given pattern, supporting
     * basic wildcard matching. It's useful for finding stores in multi-tenant
     * scenarios or when working with naming conventions.
     *
     * @param  string                            $pattern  The name pattern to match (supports * as wildcard)
     * @param  int|null                          $maxItems Maximum number of matching stores to return
     * @return FailureInterface|SuccessInterface Success containing Stores collection of matches, or Failure with error details
     */
    public function findStoresByName(string $pattern, ?int $maxItems = null): FailureInterface | SuccessInterface;

    /**
     * Gets an existing store or creates a new one with the given name.
     *
     * This convenience method first attempts to find a store by name among existing
     * stores. If no store with the given name exists, it creates a new one. This is
     * useful for idempotent store setup in development or testing scenarios.
     *
     * Note: This method lists all stores to find matches by name, which may be
     * inefficient with large numbers of stores.
     *
     * @param  string                            $name The name of the store to find or create
     * @return FailureInterface|SuccessInterface Success containing the Store (existing or new), or Failure with error details
     */
    public function getOrCreateStore(string $name): FailureInterface | SuccessInterface;

    /**
     * Lists all stores with simplified pagination.
     *
     * This method retrieves all accessible stores, automatically handling pagination
     * to return a complete collection. It abstracts away the complexity of dealing
     * with continuation tokens for most use cases.
     *
     * @param  int|null                          $maxItems Maximum number of stores to retrieve (null for all)
     * @return FailureInterface|SuccessInterface Success containing Stores collection, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Stores/ListStores List Stores API reference
     */
    public function listAllStores(?int $maxItems = null): FailureInterface | SuccessInterface;

    /**
     * Lists stores with pagination support.
     *
     * This method retrieves stores with explicit pagination control, allowing you
     * to specify continuation tokens for iterating through large result sets.
     *
     * @param  string|null                       $continuationToken Token from previous response to get next page
     * @param  int|null                          $pageSize          Maximum number of stores to return per page
     * @return FailureInterface|SuccessInterface Success containing Stores collection, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Stores/ListStores List Stores API reference
     */
    public function listStores(?string $continuationToken = null, ?int $pageSize = null): FailureInterface | SuccessInterface;
}
