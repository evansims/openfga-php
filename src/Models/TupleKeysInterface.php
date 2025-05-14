<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type TupleKeysShape = list<TupleKeyShape>
 */
interface TupleKeysInterface extends CollectionInterface
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
     * @return TupleKeysShape
     */
    public function jsonSerialize(): array;

    /**
     * Get a tuple key by offset.
     *
     * @param mixed $offset
     *
     * @return null|TupleKeyInterface
     */
    public function offsetGet(mixed $offset): ?TupleKeyInterface;

    /**
     * @param TupleKeyType   $type
     * @param TupleKeysShape $data
     */
    public static function fromArray(TupleKeyType $type, array $data): static;
}
