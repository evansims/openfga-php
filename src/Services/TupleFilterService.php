<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use OpenFGA\Models\Collections\{TupleKeys, TupleKeysInterface};
use OpenFGA\Models\{ConditionInterface, TupleKeyInterface};
use Override;

use function spl_object_hash;
use function sprintf;

/**
 * Default implementation of TupleFilterServiceInterface.
 *
 * Provides efficient duplicate filtering for tuple operations using
 * hash-based lookups to ensure O(n) complexity.
 */
final class TupleFilterService implements TupleFilterServiceInterface
{
    /**
     * @inheritDoc
     */
    #[Override]
    public function filterDuplicates(?TupleKeysInterface $writes, ?TupleKeysInterface $deletes): array
    {
        // If both are null or empty, return nulls
        if ((! $writes instanceof TupleKeysInterface || 0 === $writes->count())
            && (! $deletes instanceof TupleKeysInterface || 0 === $deletes->count())) {
            return [null, null];
        }

        // Filter writes to remove duplicates
        $uniqueWrites = [];
        $writeKeys = [];

        if ($writes instanceof TupleKeysInterface && 0 < $writes->count()) {
            foreach ($writes as $write) {
                $key = $this->getTupleKey($write);

                if (! isset($writeKeys[$key])) {
                    $writeKeys[$key] = true;
                    $uniqueWrites[] = $write;
                }
            }
        }

        // Filter deletes to remove duplicates
        $uniqueDeletes = [];
        $deleteKeys = [];

        if ($deletes instanceof TupleKeysInterface && 0 < $deletes->count()) {
            foreach ($deletes as $delete) {
                $key = $this->getTupleKey($delete);

                if (! isset($deleteKeys[$key])) {
                    $deleteKeys[$key] = true;
                    $uniqueDeletes[] = $delete;
                }
            }
        }

        // Remove from writes any tuples that also appear in deletes
        // (delete takes precedence)
        if ([] !== $uniqueWrites && [] !== $deleteKeys) {
            $finalWrites = [];

            foreach ($uniqueWrites as $uniqueWrite) {
                $key = $this->getTupleKey($uniqueWrite);

                if (! isset($deleteKeys[$key])) {
                    $finalWrites[] = $uniqueWrite;
                }
            }
            $uniqueWrites = $finalWrites;
        }

        // Return filtered collections (null if empty)
        $resultWrites = [] !== $uniqueWrites ? new TupleKeys($uniqueWrites) : null;
        $resultDeletes = [] !== $uniqueDeletes ? new TupleKeys($uniqueDeletes) : null;

        // Return null if the collections are empty
        if ($resultWrites instanceof TupleKeys && 0 === $resultWrites->count()) {
            $resultWrites = null;
        }

        if ($resultDeletes instanceof TupleKeys && 0 === $resultDeletes->count()) {
            $resultDeletes = null;
        }

        return [$resultWrites, $resultDeletes];
    }

    /**
     * Generate a unique key for a tuple based on its properties.
     *
     * Creates a hash key that uniquely identifies a tuple based on:
     * - User identifier
     * - Relation
     * - Object identifier
     * - Condition (if present)
     *
     * @param  TupleKeyInterface $tuple The tuple to generate a key for
     * @return string            A unique key for the tuple
     */
    private function getTupleKey(TupleKeyInterface $tuple): string
    {
        $condition = $tuple->getCondition();

        $conditionKey = '';

        if ($condition instanceof ConditionInterface) {
            $conditionKey = '#' . md5(json_encode($condition->jsonSerialize()));
        }

        return sprintf(
            '%s#%s@%s%s',
            $tuple->getUser(),
            $tuple->getRelation(),
            $tuple->getObject(),
            $conditionKey,
        );
    }
}
