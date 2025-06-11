<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use JsonException;
use OpenFGA\Models\Collections\{TupleKeys, TupleKeysInterface};
use OpenFGA\Models\{ConditionInterface, TupleKeyInterface};
use Override;

use function is_array;

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
        $keyData = [
            'u' => $tuple->getUser(),
            'r' => $tuple->getRelation(),
            'o' => $tuple->getObject(),
        ];

        $condition = $tuple->getCondition();

        if ($condition instanceof ConditionInterface) {
            $keyData['cond_name'] = $condition->getName();
            $context = $condition->getContext();

            if (null !== $context && [] !== $context) {
                $keyData['cond_ctx'] = $this->normalizeContext($context);
            }
        }

        try {
            return json_encode($keyData, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        } catch (JsonException) {
            return serialize($keyData);
        }
    }

    /**
     * Normalize context data by fully serializing and recursively sorting.
     *
     * This method ensures that contexts containing JsonSerializable objects
     * or other complex structures are normalized to a consistent representation
     * regardless of internal object ordering or structure.
     *
     * @param  array<mixed, mixed> $context The context array to normalize
     * @return array<mixed, mixed> The normalized and sorted context
     *
     * @psalm-suppress MixedAssignment
     */
    private function normalizeContext(array $context): array
    {
        // First, fully serialize the context to expand any JsonSerializable objects
        // and ensure all nested structures are converted to primitive arrays
        try {
            $serialized = json_encode($context, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $fullyExpanded = json_decode($serialized, true, 512, JSON_THROW_ON_ERROR);

            // Ensure json_decode returned an array
            if (! is_array($fullyExpanded)) {
                $fullyExpanded = $context;
            }
        } catch (JsonException) {
            // If JSON encoding/decoding fails, fall back to the original context
            $fullyExpanded = $context;
        }

        // Then apply recursive sorting to the fully expanded structure
        return $this->recursiveSort($fullyExpanded);
    }

    /**
     * Recursively sort arrays by keys to ensure consistent ordering.
     *
     * This method ensures that nested arrays at any depth are sorted
     * by their keys, creating a stable representation for hashing
     * regardless of the original key order.
     *
     * @param  array<mixed, mixed> $array The array to sort recursively
     * @return array<mixed, mixed> The sorted array
     *
     * @psalm-suppress MixedAssignment
     */
    private function recursiveSort(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->recursiveSort($value);
            }
        }

        ksort($array);

        return $array;
    }
}
