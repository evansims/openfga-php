<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<TypeDefinition>
 */
final class TypeDefinitions extends AbstractIndexedCollection implements TypeDefinitionsInterface
{
    protected static string $itemType = TypeDefinition::class;

    /**
     * @return null|TypeDefinitionInterface
     */
    public function current(): ?TypeDefinitionInterface
    {
        /** @var null|TypeDefinitionInterface $result */
        return parent::current();
    }

    /**
     * @param mixed $offset
     *
     * @return null|TypeDefinitionInterface
     */
    public function offsetGet(mixed $offset): ?TypeDefinitionInterface
    {
        /** @var null|TypeDefinitionInterface $result */
        $result = parent::offsetGet($offset);

        return $result instanceof TypeDefinitionInterface ? $result : null;
    }
}
