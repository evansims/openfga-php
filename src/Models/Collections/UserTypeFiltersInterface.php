<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\UserTypeFilterInterface;
use Override;

/**
 * @template T of UserTypeFilterInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface UserTypeFiltersInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{type: string, relation?: string}>
     */
    #[Override]
    public function jsonSerialize(): array;
}
