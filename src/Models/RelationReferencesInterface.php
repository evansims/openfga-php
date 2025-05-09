<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface RelationReferencesInterface extends ModelCollectionInterface
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
     * Get a relation reference by offset.
     *
     * @param mixed $offset
     *
     * @return null|RelationReferenceInterface
     */
    public function offsetGet(mixed $offset): ?RelationReferenceInterface;
}
