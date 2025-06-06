<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\TupleKeyInterface;

/**
 * Interface for writing relationship tuples to an OpenFGA store.
 *
 * This interface defines the contract for requests that modify relationship data
 * in OpenFGA stores. It supports both adding new relationships (writes) and
 * removing existing relationships (deletes) in a single atomic operation.
 *
 * Write operations are transactional, meaning either all changes succeed or
 * all changes are rolled back. This ensures data consistency when making
 * multiple related changes to the authorization graph.
 *
 * The request allows you to:
 * - Add new relationship tuples to establish permissions
 * - Remove existing relationship tuples to revoke permissions
 * - Perform both operations atomically in a single request
 * - Specify which authorization model version to use for validation
 *
 * @see TupleKeyInterface Individual relationship tuple structure
 * @see TupleKeysInterface Collection of relationship tuples
 * @see https://openfga.dev/docs/api/service#Relationship%20Tuples/Write OpenFGA Write API Documentation
 */
interface WriteTuplesRequestInterface extends RequestInterface
{
    /**
     * Get the relationship tuples to delete from the store.
     *
     * Returns a collection of relationship tuples that should be removed from
     * the authorization store. Each tuple represents a permission or relationship
     * that will be revoked. The deletion is atomic with any write operations
     * specified in the same request.
     *
     * @return TupleKeysInterface|null Collection of relationship tuples to remove, or null if no deletions are requested
     */
    public function getDeletes(): ?TupleKeysInterface;

    /**
     * Get the maximum number of parallel requests for non-transactional mode.
     *
     * @return int Maximum parallel requests (1 for sequential processing)
     */
    public function getMaxParallelRequests(): int;

    /**
     * Get the maximum number of retries for failed chunks in non-transactional mode.
     *
     * @return int Maximum retry attempts
     */
    public function getMaxRetries(): int;

    /**
     * Get the maximum number of tuples per chunk for non-transactional mode.
     *
     * @return int Maximum tuples per chunk (up to 100)
     */
    public function getMaxTuplesPerChunk(): int;

    /**
     * Get the authorization model ID to use for tuple validation.
     *
     * Specifies which version of the authorization model should be used to
     * validate the relationship tuples being written or deleted. This ensures
     * that all tuples conform to the expected schema and relationship types
     * defined in the model.
     *
     * @return string The authorization model ID for validating tuple operations
     */
    public function getModel(): string;

    /**
     * Get the retry delay in seconds for non-transactional mode.
     *
     * @return float Retry delay in seconds
     */
    public function getRetryDelaySeconds(): float;

    /**
     * Check if non-transactional processing should stop on first error.
     *
     * @return bool True to stop on first error, false to continue
     */
    public function getStopOnFirstError(): bool;

    /**
     * Get the store ID where tuples will be written.
     *
     * Identifies the OpenFGA store that contains the authorization data to
     * be modified. All write and delete operations will be performed within
     * the context of this specific store.
     *
     * @return string The store ID containing the authorization data to modify
     */
    public function getStore(): string;

    /**
     * Get the relationship tuples to write to the store.
     *
     * Returns a collection of relationship tuples that should be added to
     * the authorization store. Each tuple represents a new permission or
     * relationship that will be granted. The write operation is atomic with
     * any delete operations specified in the same request.
     *
     * @return TupleKeysInterface|null Collection of relationship tuples to add, or null if no writes are requested
     */
    public function getWrites(): ?TupleKeysInterface;

    /**
     * Check if this request should be executed in transactional mode.
     *
     * @return bool True for transactional mode, false for non-transactional
     */
    public function isTransactional(): bool;
}
