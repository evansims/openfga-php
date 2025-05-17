<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type TypeDefinitionsShape = list<TypeDefinitionShape>
 */
interface TypeDefinitionsInterface extends IndexedCollectionInterface
{
    /**
     * Add a type definition to the collection.
     *
     * @param TypeDefinitionInterface $typeDefinition
     */
    public function add(TypeDefinitionInterface $typeDefinition): void;

    /**
     * Get the current type definition in the collection.
     *
     * @return null|TypeDefinitionInterface
     */
    public function current(): ?TypeDefinitionInterface;

    /**
     * @return TypeDefinitionsShape
     */
    public function jsonSerialize(): array;

    /**
     * Get a type definition by offset.
     *
     * @param mixed $offset
     *
     * @return null|TypeDefinitionInterface
     */
    public function offsetGet(mixed $offset): ?TypeDefinitionInterface;
}
