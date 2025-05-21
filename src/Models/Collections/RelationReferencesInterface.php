<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\RelationReferenceInterface;
use Override;

/**
 * @template T of RelationReferenceInterface
 *
 * @extends KeyedCollectionInterface<T>
 */
interface RelationReferencesInterface extends KeyedCollectionInterface
{
    /**
     * Serialize the collection to an array.
     *
     * @return array<string, array{type: string, relation?: string, wildcard?: object, condition?: string}>
     */
    #[Override]
    public function jsonSerialize(): array;
}
