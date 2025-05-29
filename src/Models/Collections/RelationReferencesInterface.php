<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\RelationReferenceInterface;
use Override;

/**
 * @template T of RelationReferenceInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface RelationReferencesInterface extends IndexedCollectionInterface
{
    /**
     * Serialize the collection to an array.
     *
     * @return array<string, array{type: string, relation?: string, wildcard?: object, condition?: string}>
     */
    #[Override]
    public function jsonSerialize(): array;
}
