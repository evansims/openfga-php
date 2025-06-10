<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use Override;

/**
 * @extends IndexedCollectionInterface<\OpenFGA\Models\UsersListUserInterface>
 */
interface UsersListInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, string>
     */
    #[Override]
    public function jsonSerialize(): array;
}
