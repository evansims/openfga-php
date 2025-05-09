<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface TupleChangesInterface extends ModelCollectionInterface
{
    /**
     * Add a tuple change to the collection.
     *
     * @param TupleChangeInterface $tupleChange
     */
    public function add(TupleChangeInterface $tupleChange): void;

    /**
     * Get the current tuple change in the collection.
     *
     * @return TupleChangeInterface
     */
    public function current(): TupleChangeInterface;

    /**
     * Get a tuple change by offset.
     *
     * @param mixed $offset
     *
     * @return null|TupleChangeInterface
     */
    public function offsetGet(mixed $offset): ?TupleChangeInterface;
}
