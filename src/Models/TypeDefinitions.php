<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @implements \ArrayAccess<int, TypeDefinitionInterface>
 * @implements \Iterator<int, TypeDefinitionInterface>
 */
final class TypeDefinitions extends AbstractIndexedCollection implements TypeDefinitionsInterface
{
    /**
     * @var class-string<TypeDefinitionInterface>
     */
    protected static string $itemType = TypeDefinition::class;

    /**
     * @param iterable<TypeDefinitionInterface>|TypeDefinitionInterface ...$typeDefinitions
     */
    public function __construct(iterable | TypeDefinitionInterface ...$typeDefinitions)
    {
        parent::__construct(...$typeDefinitions);
    }
}
