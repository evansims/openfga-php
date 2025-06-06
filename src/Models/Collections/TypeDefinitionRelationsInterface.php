<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use Override;

/**
 * @extends KeyedCollectionInterface<\OpenFGA\Models\UsersetInterface>
 */
interface TypeDefinitionRelationsInterface extends KeyedCollectionInterface
{
    /**
     * Serialize the collection to an array.
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array;
}
