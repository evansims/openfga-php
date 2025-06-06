<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\UsersetInterface;
use Override;

/**
 * @extends KeyedCollectionInterface<UsersetInterface>
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
