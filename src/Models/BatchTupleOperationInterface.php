<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Schema\SchemaInterface;
use Override;

/**
 * Interface for batch tuple operations.
 *
 * Defines the contract for organizing tuple writes and deletes into batches
 * that can be processed efficiently while respecting API limitations.
 */
interface BatchTupleOperationInterface extends ModelInterface
{
    /**
     * Get the JSON schema for this model.
     *
     * @return SchemaInterface The schema definition
     */
    #[Override]
    public static function schema(): SchemaInterface;

    /**
     * Split this operation into smaller chunks that respect API limits.
     *
     * If the operation doesn't require chunking, returns an array containing
     * only this operation. Otherwise, splits the writes and deletes across
     * multiple operations to stay within the specified chunk size.
     *
     * @param  int                                 $chunkSize Maximum tuples per chunk (default: API limit)
     * @return array<BatchTupleOperationInterface> Array of operations, each within the chunk size
     */
    public function chunk(int $chunkSize = 100): array;

    /**
     * Get the tuples to delete in this operation.
     *
     * @return TupleKeysInterface<TupleKeyInterface>|null Collection of tuples to delete, or null if none
     */
    public function getDeletes(): ?TupleKeysInterface;

    /**
     * Get the total number of operations (writes + deletes).
     *
     * @return int Total count of tuples to be processed
     */
    public function getTotalOperations(): int;

    /**
     * Get the tuples to write in this operation.
     *
     * @return TupleKeysInterface<TupleKeyInterface>|null Collection of tuples to write, or null if none
     */
    public function getWrites(): ?TupleKeysInterface;

    /**
     * Check if this operation is empty (no writes or deletes).
     *
     * @return bool True if no operations are defined
     */
    public function isEmpty(): bool;

    /**
     * Check if this operation requires chunking due to size limits.
     *
     * @param  int  $chunkSize Maximum tuples per chunk (default: API limit)
     * @return bool True if the operation exceeds the specified chunk size
     */
    public function requiresChunking(int $chunkSize = 100): bool;
}
