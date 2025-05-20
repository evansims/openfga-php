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
     * Add a user to the collection.
     *
     * @param T $user
     */
    public function add(UsersListUserInterface $user): void;

    /**
     * Get the current user in the collection.
     *
     * @return T
     */
    public function current(): UsersListUserInterface;

    /**
     * @return array<int, string>
     */
    public function jsonSerialize(): array;

    /**
     * Get a user by offset.
     *
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?UsersListUserInterface;
}
