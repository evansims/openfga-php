<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\TupleChangeInterface;

/**
 * @template T of TupleChangeInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface TupleChangesInterface extends IndexedCollectionInterface
{
    /**
     * Add a tuple change to the collection.
     *
     * @param T $tupleChange
     */
    public function add(TupleChangeInterface $tupleChange): void;

    /**
     * Get the current tuple change in the collection.
     *
     * @return T
     */
    public function current(): TupleChangeInterface;

    /**
     * @return array<int, array{
     *     tuple_key: array{
     *         user: string,
     *         relation: string,
     *         object: string,
     *         condition?: array<string, mixed>,
     *     },
     *     operation: string,
     *     timestamp: string,
     * }>
     */
    public function jsonSerialize(): array;

    /**
     * Get a tuple change by offset.
     *
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?TupleChangeInterface;
}
