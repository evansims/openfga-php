<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface ObjectsInterface extends ModelCollectionInterface
{
    /**
     * Add a store to the collection.
     *
     * @param ObjectInterface $object
     */
    public function add(ObjectInterface $object): void;

    /**
     * Get the current store in the collection.
     *
     * @return ObjectInterface
     */
    public function current(): ObjectInterface;

    /**
     * Get a store by offset.
     *
     * @param mixed $offset
     *
     * @return null|ObjectInterface
     */
    public function offsetGet(mixed $offset): ?ObjectInterface;
}
