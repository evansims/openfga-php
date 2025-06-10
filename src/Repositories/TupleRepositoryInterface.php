<?php

declare(strict_types=1);

namespace OpenFGA\Repositories;

use DateTimeImmutable;
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface, TupleKeyInterface};
use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Results\{FailureInterface, SuccessInterface};

/**
 * Repository contract for relationship tuple operations.
 *
 * This interface defines the contract for managing relationship tuples within an OpenFGA store.
 * Tuples represent relationships between users and objects (e.g., "user:anne is reader of document:budget"),
 * forming the core data that drives authorization decisions. The repository supports both
 * transactional and non-transactional operations for different scale and consistency requirements.
 *
 * All methods return Result objects following the Result pattern, allowing for consistent
 * error handling without exceptions.
 *
 * @see https://openfga.dev/docs/concepts#what-is-a-relationship-tuple Understanding relationship tuples
 */
interface TupleRepositoryInterface
{
    /**
     * Delete relationship tuples from the store.
     *
     * Removes existing relationship tuples from the store. Like write operations,
     * supports both transactional and non-transactional modes with the same
     * constraints and options.
     *
     * @param  StoreInterface                    $store         The store containing the tuples
     * @param  AuthorizationModelInterface       $model         The authorization model to validate against
     * @param  TupleKeysInterface                $tuples        The tuples to delete
     * @param  bool                              $transactional Whether to use transactional mode (default: true)
     * @param  array<string, mixed>              $options       Additional options (same as write method)
     * @return FailureInterface|SuccessInterface Success with WriteTuplesResponse containing operation results, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Relationship%20Tuples/Write Deleting tuples via the write endpoint
     */
    public function delete(
        StoreInterface $store,
        AuthorizationModelInterface $model,
        TupleKeysInterface $tuples,
        bool $transactional = true,
        array $options = [],
    ): FailureInterface | SuccessInterface;

    /**
     * List changes to relationship tuples over time.
     *
     * Retrieves a chronological log of tuple changes (writes and deletes) within the store.
     * Useful for auditing, synchronization, or understanding how relationships evolved.
     * Results can be filtered by object type and time range.
     *
     * @param  StoreInterface                    $store             The store to query
     * @param  string|null                       $type              Filter by object type (e.g., "document")
     * @param  DateTimeImmutable|null            $startTime         Filter changes after this time
     * @param  string|null                       $continuationToken Token from previous response for pagination
     * @param  int|null                          $pageSize          Maximum number of changes to return
     * @return FailureInterface|SuccessInterface Success with ListTupleChangesResponse containing change history, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Relationship%20Tuples/ListChanges Viewing tuple change history
     */
    public function listChanges(
        StoreInterface $store,
        ?string $type = null,
        ?DateTimeImmutable $startTime = null,
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Read relationship tuples from the store.
     *
     * Retrieves tuples matching the specified filter criteria. The filter uses partial
     * matching - you can specify any combination of user, relation, and object to narrow
     * results. Results are paginated for efficient retrieval of large datasets.
     *
     * @param  StoreInterface                    $store             The store containing the tuples
     * @param  TupleKeyInterface                 $filter            Filter criteria for tuple matching
     * @param  string|null                       $continuationToken Token from previous response for pagination
     * @param  int|null                          $pageSize          Maximum number of tuples to return (1-100)
     * @return FailureInterface|SuccessInterface Success with ReadTuplesResponse containing matching tuples, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Relationship%20Tuples/Read Reading tuples from the store
     */
    public function read(
        StoreInterface $store,
        TupleKeyInterface $filter,
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Write relationship tuples to the store.
     *
     * Creates new relationship tuples in the store. Supports both transactional mode
     * (all-or-nothing, limited to 100 tuples) and non-transactional mode for larger
     * batches with configurable parallelism and retry behavior.
     *
     * @param  StoreInterface                    $store         The store to write tuples to
     * @param  AuthorizationModelInterface       $model         The authorization model to validate against
     * @param  TupleKeysInterface                $tuples        The tuples to write
     * @param  bool                              $transactional Whether to use transactional mode (default: true)
     * @param  array<string, mixed>              $options       Additional options for non-transactional mode:
     *                                                          - maxParallelRequests: int (default: 1)
     *                                                          - maxTuplesPerChunk: int (default: 100)
     *                                                          - maxRetries: int (default: 0)
     *                                                          - retryDelaySeconds: float (default: 1.0)
     *                                                          - stopOnFirstError: bool (default: false)
     * @return FailureInterface|SuccessInterface Success with WriteTuplesResponse containing operation results, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Relationship%20Tuples/Write Writing tuples to the store
     */
    public function write(
        StoreInterface $store,
        AuthorizationModelInterface $model,
        TupleKeysInterface $tuples,
        bool $transactional = true,
        array $options = [],
    ): FailureInterface | SuccessInterface;

    /**
     * Write and delete relationship tuples in a single operation.
     *
     * Combines write and delete operations for efficiency, especially useful when
     * you need to atomically replace relationships. In transactional mode, all
     * operations succeed or fail together. In non-transactional mode, operations
     * are batched for optimal performance.
     *
     * @param  StoreInterface                    $store         The store to operate on
     * @param  AuthorizationModelInterface       $model         The authorization model to validate against
     * @param  TupleKeysInterface|null           $writes        Tuples to write (optional)
     * @param  TupleKeysInterface|null           $deletes       Tuples to delete (optional)
     * @param  bool                              $transactional Whether to use transactional mode (default: true)
     * @param  array<string, mixed>              $options       Additional options (same as write/delete methods)
     * @return FailureInterface|SuccessInterface Success with WriteTuplesResponse containing operation results, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Relationship%20Tuples/Write Combined write/delete operations
     */
    public function writeAndDelete(
        StoreInterface $store,
        AuthorizationModelInterface $model,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
        bool $transactional = true,
        array $options = [],
    ): FailureInterface | SuccessInterface;
}
