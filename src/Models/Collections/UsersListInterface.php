<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\UsersListUserInterface;
use Override;

/**
 * @extends IndexedCollectionInterface<UsersListUserInterface>
 */
interface UsersListInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, string>
     */
    #[Override]
    public function jsonSerialize(): array;
}
