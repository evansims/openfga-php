<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\CollectionSchemaInterface;

/**
 * @extends IndexedCollection<TypeDefinition>
 */
final class TypeDefinitions extends IndexedCollection implements TypeDefinitionsInterface
{
    protected static string $itemType = TypeDefinition::class;

    protected static ?CollectionSchemaInterface $schema = null;

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
