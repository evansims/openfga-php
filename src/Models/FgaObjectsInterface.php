<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type FgaObjectsShape = list<FgaObjectShape>
 */
interface FgaObjectsInterface extends CollectionInterface
{
    /**
     * Add a object to the collection.
     *
     * @param FgaObjectInterface $object
     */
    public function add(FgaObjectInterface $object): void;

    /**
     * Get the current object in the collection.
     *
     * @return FgaObjectInterface
     */
    public function current(): FgaObjectInterface;

    /**
     * Get a object by offset.
     *
     * @param mixed $offset
     *
     * @return null|FgaObjectInterface
     */
    public function offsetGet(mixed $offset): ?FgaObjectInterface;
}
