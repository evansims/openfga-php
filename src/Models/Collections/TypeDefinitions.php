<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{TypeDefinition, TypeDefinitionInterface};

/**
 * @extends IndexedCollection<TypeDefinitionInterface>
 *
 * @implements TypeDefinitionsInterface<TypeDefinitionInterface>
 */
final class TypeDefinitions extends IndexedCollection implements TypeDefinitionsInterface
{
    protected static string $itemType = TypeDefinition::class;
}
