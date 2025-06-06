<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use Override;

/**
 * Represents a collection of usersets used in authorization model operations.
 *
 * This collection manages multiple userset definitions that are used in
 * complex authorization operations like unions, intersections, and differences.
 * Each userset in the collection can define different ways to compute authorized users.
 *
 * @extends IndexedCollectionInterface<\OpenFGA\Models\UsersetInterface>
 */
interface UsersetsInterface extends IndexedCollectionInterface
{
    /**
     * @return array{child: array<mixed>}
     */
    #[Override]
    public function jsonSerialize(): array;
}
