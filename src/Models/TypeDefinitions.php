<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<TypeDefinition>
 */
final class TypeDefinitions extends AbstractIndexedCollection implements TypeDefinitionsInterface
{
    protected static string $itemType = TypeDefinition::class;
}
