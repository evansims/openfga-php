<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type UsersListShape = list<UserListUserShape>
 */
interface UsersListInterface extends IndexedCollectionInterface
{
    /**
     * Add a user to the collection.
     *
     * @param UsersListUserInterface $user
     */
    public function add(UsersListUserInterface $user): void;

    /**
     * Get the current user in the collection.
     *
     * @return null|UsersListUserInterface
     */
    public function current(): ?UsersListUserInterface;

    /**
     * @return UsersListShape
     */
    public function jsonSerialize(): array;

    /**
     * Get a user by offset.
     *
     * @param mixed $offset
     *
     * @return null|UsersListUserInterface
     */
    public function offsetGet(mixed $offset): ?UsersListUserInterface;
}
