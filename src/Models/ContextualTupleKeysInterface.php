<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface ContextualTupleKeysInterface extends ModelCollectionInterface
{
    /**
     * Add a tuple key to the collection.
     *
     * @param ContextualTupleKeyInterface $tupleKey
     */
    public function add(ContextualTupleKeyInterface $tupleKey): void;

    /**
     * Get the current tuple key in the collection.
     *
     * @return ContextualTupleKeyInterface
     */
    public function current(): ContextualTupleKeyInterface;

    /**
     * Get a tuple key by offset.
     *
     * @param mixed $offset
     *
     * @return null|ContextualTupleKeyInterface
     */
    public function offsetGet(mixed $offset): ?ContextualTupleKeyInterface;
}
