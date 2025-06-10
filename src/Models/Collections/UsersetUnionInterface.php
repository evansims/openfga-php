<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use Override;

/**
 * Represents a specialized collection for userset union operations.
 *
 * This collection is specifically designed for managing usersets in union operations,
 * where users are granted access if they satisfy ANY of the contained usersets.
 * The collection provides optimized serialization for union-specific data structures.
 *
 * @extends IndexedCollectionInterface<\OpenFGA\Models\UsersetInterface>
 */
interface UsersetUnionInterface extends IndexedCollectionInterface
{
    /**
     * @return array{
     *     child: array<int|string, mixed>
     * }
     */
    #[Override]
    public function jsonSerialize(): array;
}
