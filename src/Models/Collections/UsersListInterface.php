<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\UsersListUserInterface;
use Override;

/**
 * @template T of UsersListUserInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface UsersListInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, string>
     */
    #[Override]
    public function jsonSerialize(): array;
}
