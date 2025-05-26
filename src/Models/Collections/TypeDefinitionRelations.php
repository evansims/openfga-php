<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{UsersetInterface, Userset};

/**
 * @extends KeyedCollection<UsersetInterface>
 *
 * @implements TypeDefinitionRelationsInterface<UsersetInterface>
 */
final class TypeDefinitionRelations extends KeyedCollection implements TypeDefinitionRelationsInterface
{
    protected static string $itemType = Userset::class;
}
