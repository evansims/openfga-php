<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type RelationReferencesShape = array<string, RelationReferenceShape>
 *
 * @extends KeyedCollectionInterface<string, RelationReferenceInterface>
 */
interface RelationReferencesInterface extends KeyedCollectionInterface
{
    /**
     * Add a relation reference to the collection.
     *
     * @param string                     $key
     * @param RelationReferenceInterface $relationReference
     */
    public function add(string $key, RelationReferenceInterface $relationReference): void;

    /**
     * Get the current relation reference in the collection.
     *
     * @return null|RelationReferenceInterface
     */
    public function current(): ?RelationReferenceInterface;

    /**
     * Serialize the collection to an array.
     *
     * @return RelationReferencesShape
     */
    public function jsonSerialize(): array;

    /**
     * Get a relation reference by offset.
     *
     * @param mixed $offset
     *
     * @return null|RelationReferenceInterface
     */
    public function offsetGet(mixed $offset): ?RelationReferenceInterface;
}
