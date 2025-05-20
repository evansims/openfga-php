<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\TupleKeyInterface;

/**
 * @template T of TupleKeyInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface TupleKeysInterface extends IndexedCollectionInterface
{
    /**
     * Add a tuple key to the collection.
     *
     * @param T $tupleKey
     */
    public function add(TupleKeyInterface $tupleKey): void;

    /**
     * Get the current tuple key in the collection.
     *
     * @return T
     */
    public function current(): TupleKeyInterface;

    /**
     * @return array<int, array{user: string, relation: string, object: string, condition?: array<string, mixed>}>
     */
    public function jsonSerialize(): array;

    /**
     * Get a tuple key by offset.
     *
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?TupleKeyInterface;
}
