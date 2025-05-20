<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\TupleInterface;

/**
 * @template T of TupleInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface TuplesInterface extends IndexedCollectionInterface
{
    /**
     * Add a tuple to the collection.
     *
     * @param T $tuple
     */
    public function add(TupleInterface $tuple): void;

    /**
     * Get the current tuple in the collection.
     *
     * @return T
     */
    public function current(): TupleInterface;

    /**
     * @return array<int, array{key: array{user: string, relation: string, object: string, condition?: array<string, mixed>}, timestamp: string}>
     */
    public function jsonSerialize(): array;

    /**
     * Get a tuple by offset.
     *
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?TupleInterface;
}
