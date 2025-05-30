<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\RelationMetadataInterface;

/**
 * @template T of RelationMetadataInterface
 *
 * @extends KeyedCollectionInterface<T>
 */
interface RelationMetadataCollectionInterface extends KeyedCollectionInterface
{
}
