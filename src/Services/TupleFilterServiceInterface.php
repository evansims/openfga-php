<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use OpenFGA\Models\Collections\TupleKeysInterface;

/**
 * Service for filtering and deduplicating tuple operations.
 *
 * This service encapsulates the business logic for handling duplicate tuples
 * in write and delete operations, ensuring that:
 * - No duplicate tuples exist within writes or deletes
 * - Delete operations take precedence over writes when conflicts occur
 * - Order is preserved based on first occurrence
 */
interface TupleFilterServiceInterface
{
    /**
     * Filter duplicate tuples from writes and deletes collections.
     *
     * This method ensures that:
     * 1. No duplicate tuples exist within the writes collection
     * 2. No duplicate tuples exist within the deletes collection
     * 3. If a tuple appears in both writes and deletes, it's removed from writes
     *    (delete takes precedence to ensure the final state is deletion)
     *
     * @param  TupleKeysInterface|null                                       $writes  The writes to filter
     * @param  TupleKeysInterface|null                                       $deletes The deletes to filter
     * @return array{0: TupleKeysInterface|null, 1: TupleKeysInterface|null} Filtered writes and deletes
     */
    public function filterDuplicates(?TupleKeysInterface $writes, ?TupleKeysInterface $deletes): array;
}
