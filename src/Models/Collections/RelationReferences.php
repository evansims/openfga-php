<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{RelationReference, RelationReferenceInterface};

/**
 * @extends IndexedCollection<RelationReferenceInterface>
 *
 * @implements RelationReferencesInterface<RelationReferenceInterface>
 */
final class RelationReferences extends IndexedCollection implements RelationReferencesInterface
{
    protected static string $itemType = RelationReference::class;
}
