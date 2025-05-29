<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{RelationMetadata, RelationMetadataInterface};

/**
 * @extends KeyedCollection<RelationMetadataInterface>
 *
 * @implements RelationMetadataCollectionInterface<RelationMetadataInterface>
 */
final class RelationMetadataCollection extends KeyedCollection implements RelationMetadataCollectionInterface
{
    protected static string $itemType = RelationMetadata::class;
}
