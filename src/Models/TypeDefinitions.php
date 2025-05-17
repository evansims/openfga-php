<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of TypeDefinitionInterface
 * @extends AbstractIndexedCollection<T>
 */
final class TypeDefinitions extends AbstractIndexedCollection implements TypeDefinitionsInterface
{
    /**
     * @var class-string<T>
     */
    protected static string $itemType = TypeDefinition::class;

    /**
     * @param list<T>|T ...$typeDefinitions
     */
    public function __construct(iterable | TypeDefinitionInterface ...$typeDefinitions)
    {
        parent::__construct(...$typeDefinitions);
    }
}
