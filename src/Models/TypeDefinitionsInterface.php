<?php

namespace OpenFGA\Models;

interface TypeDefinitionsInterface extends ModelCollectionInterface
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
     * @return TypeDefinitionInterface
     */
    public function current(): TypeDefinitionInterface;

    /**
     * Get a type definition by offset.
     *
     * @param mixed $offset
     *
     * @return TypeDefinitionInterface|null
     */
    public function offsetGet(mixed $offset): ?TypeDefinitionInterface;
}
