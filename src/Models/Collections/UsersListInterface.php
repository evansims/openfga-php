<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\UsersListUserInterface;

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
    public function jsonSerialize(): array;
}
