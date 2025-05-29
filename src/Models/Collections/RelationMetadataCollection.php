<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{RelationMetadata, RelationMetadataInterface};

/**
 * @extends KeyedCollection<RelationMetadataInterface>
 */
final class RelationMetadataCollection extends KeyedCollection
{
    protected static string $itemType = RelationMetadata::class;
}
