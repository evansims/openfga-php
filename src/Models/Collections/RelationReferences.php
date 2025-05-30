<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{ModelInterface, RelationReference, RelationReferenceInterface};

/**
 * @extends IndexedCollection<RelationReferenceInterface>
 *
 * @implements RelationReferencesInterface<RelationReferenceInterface>
 */
final class RelationReferences extends IndexedCollection implements RelationReferencesInterface
{
    /**
     * @phpstan-var class-string<RelationReferenceInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = RelationReference::class;
}
