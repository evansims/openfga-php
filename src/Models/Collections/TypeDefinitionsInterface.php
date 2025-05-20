<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\TypeDefinitionInterface;

/**
 * @template T of TypeDefinitionInterface
 * @extends IndexedCollectionInterface<T>
 */
interface TypeDefinitionsInterface extends IndexedCollectionInterface
{
    /**
     * Add a type definition to the collection.
     *
     * @param T $typeDefinition
     */
    public function add(TypeDefinitionInterface $typeDefinition): void;

    /**
     * Get the current type definition in the collection.
     *
     * @return T
     */
    public function current(): TypeDefinitionInterface;

    /**
     * @return array<int, array{
     *     type: string,
     *     relations?: array<string, mixed>,
     *     metadata?: array<string, mixed>,
     * }>
     */
    public function jsonSerialize(): array;

    /**
     * Get a type definition by offset.
     *
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?TypeDefinitionInterface;
}
