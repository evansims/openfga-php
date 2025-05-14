<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type TuplesShape = list<TupleShape>
 */
interface TuplesInterface extends CollectionInterface
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
     * @return TuplesShape
     */
    public function jsonSerialize(): array;

    /**
     * Get a tuple by offset.
     *
     * @param mixed $offset
     *
     * @return null|TupleInterface
     */
    public function offsetGet(mixed $offset): ?TupleInterface;

    /**
     * @param TuplesShape $data
     */
    public static function fromArray(array $data): self;
}
