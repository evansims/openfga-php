<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use DateTimeImmutable;
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface, TupleKeyInterface};
use OpenFGA\Models\Collections\{TupleKeysInterface};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Results\{FailureInterface, SuccessInterface};

/**
 * Service interface for managing OpenFGA relationship tuples.
 *
 * This service provides business-focused operations for working with relationship
 * tuples, which represent the core relationships in your authorization model.
 * Tuples define who has what relationship to which objects, forming the foundation
 * of your permission system.
 *
 * ## Core Operations
 *
 * The service supports tuple management with enhanced functionality:
 * - Write tuples with validation and duplicate filtering
 * - Read tuples with flexible filtering and pagination
 * - Delete tuples safely with existence checking
 * - Track tuple changes over time for auditing
 *
 * ## Batch Operations
 *
 * For high-throughput scenarios, the service provides:
 * - Batch writes for multiple tuples in a single operation
 * - Transaction support for atomicity guarantees
 * - Automatic chunking to respect API limits
 * - Duplicate filtering to optimize performance
 *
 * ## Usage Example
 *
 * ```php
 * $tupleService = new TupleService($tupleRepository);
 *
 * // Write a single tuple
 * $result = $tupleService->write(
 *     $store,
 *     'user:anne',
 *     'reader',
 *     'document:budget-2024'
 * );
 *
 * // Read tuples with filters
 * $tuples = $tupleService->read(
 *     $store,
 *     user: 'user:anne',
 *     relation: 'reader'
 * )->unwrap();
 *
 * // Write multiple tuples
 * $batch = $tupleService->writeBatch($store, $tupleKeys)->unwrap();
 * ```
 *
 * @see TupleRepositoryInterface Underlying repository for data access
 * @see TupleInterface Individual tuple representation
 */
interface TupleServiceInterface
{
    /**
     * Delete a single relationship tuple.
     *
     * Removes the specified relationship, with optional existence checking
     * to provide better error messages when the tuple doesn't exist.
     *
     * @param  StoreInterface|string             $store         The store containing the tuple
     * @param  string                            $user          The user identifier
     * @param  string                            $relation      The relationship type
     * @param  string                            $object        The object identifier
     * @param  bool                              $confirmExists Whether to check tuple exists before deletion (default: false)
     * @return FailureInterface|SuccessInterface Success if deleted, or Failure with error details
     */
    public function delete(
        StoreInterface | string $store,
        string $user,
        string $relation,
        string $object,
        bool $confirmExists = false,
    ): FailureInterface | SuccessInterface;

    /**
     * Delete multiple relationship tuples in a batch operation.
     *
     * Efficiently removes multiple tuples, with automatic chunking and optional
     * existence checking for better error reporting.
     *
     * @param  StoreInterface|string             $store         The store containing the tuples
     * @param  TupleKeysInterface                $tupleKeys     The tuples to delete
     * @param  bool                              $transactional Whether to use transactional deletes (default: true)
     * @param  bool                              $confirmExists Whether to check tuples exist before deletion (default: false)
     * @return FailureInterface|SuccessInterface Success if all deleted, or Failure with error details
     */
    public function deleteBatch(
        StoreInterface | string $store,
        TupleKeysInterface $tupleKeys,
        bool $transactional = true,
        bool $confirmExists = false,
    ): FailureInterface | SuccessInterface;

    /**
     * Check if a specific tuple exists in the store.
     *
     * Efficiently verifies tuple existence without retrieving all matching tuples.
     * Useful for validation before operations or conditional logic.
     *
     * @param  StoreInterface|string             $store    The store to check
     * @param  string                            $user     The user identifier
     * @param  string                            $relation The relationship type
     * @param  string                            $object   The object identifier
     * @return FailureInterface|SuccessInterface Success with true/false, or Failure with error details
     */
    public function exists(
        StoreInterface | string $store,
        string $user,
        string $relation,
        string $object,
    ): FailureInterface | SuccessInterface;

    /**
     * Get statistics about tuples in the store.
     *
     * Provides insights into the tuple distribution and counts by type and relation,
     * useful for monitoring and capacity planning.
     *
     * @param  StoreInterface|string             $store The store to analyze
     * @return FailureInterface|SuccessInterface Success with statistics array, or Failure with error details
     */
    public function getStatistics(
        StoreInterface | string $store,
    ): FailureInterface | SuccessInterface;

    /**
     * List changes to tuples over time for auditing purposes.
     *
     * Retrieves a chronological log of tuple changes (writes and deletes)
     * within the specified time range, useful for compliance and debugging.
     *
     * @param  StoreInterface|string             $store             The store to list changes from
     * @param  string|null                       $type              Filter by object type (optional)
     * @param  DateTimeImmutable|null            $startTime         Start time for changes (optional)
     * @param  string|null                       $continuationToken Token for pagination (optional)
     * @param  int|null                          $pageSize          Maximum number of changes to retrieve (default: 100)
     * @return FailureInterface|SuccessInterface Success with changes collection, or Failure with error details
     */
    public function listChanges(
        StoreInterface | string $store,
        ?string $type = null,
        ?DateTimeImmutable $startTime = null,
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Read relationship tuples with optional filtering.
     *
     * Retrieves tuples matching the specified criteria, with automatic pagination
     * handling for large result sets.
     *
     * @param  StoreInterface|string             $store             The store to read from
     * @param  TupleKeyInterface|null            $tupleKey          The tuple key to filter by (optional)
     * @param  string|null                       $continuationToken Token for pagination (optional)
     * @param  int|null                          $pageSize          Maximum number of tuples to retrieve (null for all)
     * @param  Consistency|null                  $consistency       Read consistency level (optional)
     * @return FailureInterface|SuccessInterface Success with tuples collection, or Failure with error details
     */
    public function read(
        StoreInterface | string $store,
        ?TupleKeyInterface $tupleKey = null,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Write a single relationship tuple.
     *
     * Creates a relationship between a user and an object with the specified
     * relation. This is the most common operation for establishing permissions.
     *
     * @param  StoreInterface|string             $store    The store where the tuple will be written
     * @param  string                            $user     The user identifier (for example 'user:anne')
     * @param  string                            $relation The relationship type (for example 'reader', 'writer')
     * @param  string                            $object   The object identifier (for example 'document:budget-2024')
     * @return FailureInterface|SuccessInterface Success if written, or Failure with error details
     */
    public function write(
        StoreInterface | string $store,
        string $user,
        string $relation,
        string $object,
    ): FailureInterface | SuccessInterface;

    /**
     * Write multiple relationship tuples in a batch operation.
     *
     * Efficiently writes multiple tuples, with automatic chunking to respect
     * API limits and optional duplicate filtering for performance optimization.
     *
     * @param  StoreInterface|string              $store               The store where tuples will be written
     * @param  AuthorizationModelInterface|string $model               The authorization model to use
     * @param  TupleKeysInterface|null            $writes              The tuples to write (optional)
     * @param  TupleKeysInterface|null            $deletes             The tuples to delete (optional)
     * @param  bool                               $transactional       Whether to use transactional writes (default: true)
     * @param  int                                $maxParallelRequests Maximum parallel requests (default: 1)
     * @param  int                                $maxTuplesPerChunk   Maximum tuples per chunk (default: 100)
     * @param  int                                $maxRetries          Maximum retries (default: 0)
     * @param  float                              $retryDelaySeconds   Retry delay in seconds (default: 1.0)
     * @param  bool                               $stopOnFirstError    Whether to stop on first error (default: false)
     * @return FailureInterface|SuccessInterface  Success if all written, or Failure with error details
     */
    public function writeBatch(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
        bool $transactional = true,
        int $maxParallelRequests = 1,
        int $maxTuplesPerChunk = 100,
        int $maxRetries = 0,
        float $retryDelaySeconds = 1.0,
        bool $stopOnFirstError = false,
    ): FailureInterface | SuccessInterface;
}
