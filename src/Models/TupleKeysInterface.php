<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface TupleKeysInterface extends ModelCollectionInterface
{
    /**
     * Add a tuple key to the collection.
     *
     * @param TupleKeyInterface $tupleKey
     */
    public function add(TupleKeyInterface $tupleKey): void;

    /**
     * Get the current tuple key in the collection.
     *
     * @return TupleKeyInterface
     */
    public function current(): TupleKeyInterface;

    /**
     * Get a tuple key by offset.
     *
     * @param mixed $offset
     *
     * @return null|TupleKeyInterface
     */
    public function offsetGet(mixed $offset): ?TupleKeyInterface;
}
