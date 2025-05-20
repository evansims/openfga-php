<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\UsersetInterface;

/**
 * @template T of UsersetInterface
 * @extends KeyedCollectionInterface<T>
 */
interface UsersetsInterface extends KeyedCollectionInterface
{
    /**
     * Add a userset to the collection.
     *
     * @param T $userset
     */
    public function add(UsersetInterface $userset): void;

    /**
     * @return UsersetsShape
     */
    public function jsonSerialize(): array;
}
