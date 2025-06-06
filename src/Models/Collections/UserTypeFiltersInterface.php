<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use Override;

/**
 * @extends IndexedCollectionInterface<\OpenFGA\Models\UserTypeFilterInterface>
 */
interface UserTypeFiltersInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{type: string, relation?: string}>
     */
    #[Override]
    public function jsonSerialize(): array;
}
