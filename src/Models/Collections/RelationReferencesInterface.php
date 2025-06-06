<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use Override;

/**
 * @extends IndexedCollectionInterface<\OpenFGA\Models\RelationReferenceInterface>
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
