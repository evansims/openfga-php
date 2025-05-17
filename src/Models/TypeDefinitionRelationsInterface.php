<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type TypeDefinitionRelationsShape = array<string, UsersetShape>
 *
 * @extends KeyedCollectionInterface<string, UsersetInterface>
 *
 * @implements \ArrayAccess<string, UsersetInterface>
 * @implements \Iterator<string, UsersetInterface>
 */
interface TypeDefinitionRelationsInterface extends KeyedCollectionInterface
{
    /**
     * Add a userset to the collection.
     *
     * @param string           $key
     * @param UsersetInterface $userset
     */
    public function add(string $key, UsersetInterface $userset): void;

    /**
     * Get the current userset in the collection.
     *
     * @return UsersetInterface
     */
    public function current(): UsersetInterface;

    /**
     * Serialize the collection to an array.
     *
     * @return TypeDefinitionRelationsShape
     */
    public function jsonSerialize(): array;

    /**
     * Get a userset by offset.
     *
     * @param mixed $offset
     *
     * @return null|UsersetInterface
     */
    public function offsetGet(mixed $offset): ?UsersetInterface;
}
