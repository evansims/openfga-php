<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface TuplesInterface extends ModelCollectionInterface
{
    /**
     * Add a tuple to the collection.
     *
     * @param TupleInterface $tuple
     */
    public function add(TupleInterface $tuple): void;

    /**
     * Get the current tuple in the collection.
     *
     * @return TupleInterface
     */
    public function current(): TupleInterface;

    /**
     * Get a tuple by offset.
     *
     * @param mixed $offset
     *
     * @return null|TupleInterface
     */
    public function offsetGet(mixed $offset): ?TupleInterface;
}
