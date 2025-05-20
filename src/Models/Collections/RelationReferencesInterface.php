<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\RelationReferenceInterface;

/**
 * @template T of RelationReferenceInterface
 * @extends KeyedCollectionInterface<T>
 */
interface RelationReferencesInterface extends KeyedCollectionInterface
{
    /**
     * Add a relation reference to the collection.
     *
     * @param string $key
     * @param T      $relationReference
     */
    public function add(string $key, RelationReferenceInterface $relationReference): void;

    /**
     * Get the current relation reference in the collection.
     *
     * @return T
     */
    public function current(): RelationReferenceInterface;

    /**
     * Serialize the collection to an array.
     *
     * @return array<string, array{type: string, relation?: string, wildcard?: object, condition?: string}>
     */
    public function jsonSerialize(): array;

    /**
     * Get a relation reference by offset.
     *
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?RelationReferenceInterface;
}
