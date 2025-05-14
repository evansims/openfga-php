<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type RelationReferencesShape = array<string, RelationReferenceShape>
 */
interface RelationReferencesInterface extends CollectionInterface
{
    /**
     * Add a relation reference to the collection.
     *
     * @param RelationReferenceInterface $relationReference
     */
    public function add(RelationReferenceInterface $relationReference): void;

    /**
     * Get the current relation reference in the collection.
     *
     * @return RelationReferenceInterface
     */
    public function current(): RelationReferenceInterface;

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

    /**
     * Create a collection from an array.
     *
     * @param RelationReferencesShape $data
     *
     * @return self
     */
    public static function fromArray(array $data): self;
}
