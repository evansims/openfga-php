<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\RelationReferenceInterface;

/**
 * @extends KeyedCollection<RelationReferenceInterface>
 *
 * @implements RelationReferencesInterface<RelationReferenceInterface>
 */
final class RelationReferences extends KeyedCollection implements RelationReferencesInterface
{
    protected static string $itemType = RelationReferenceInterface::class;
}
