<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type UsersShape = list<UserShape>
 */
interface UsersInterface extends CollectionInterface
{
    /**
     * Add a user to the collection.
     *
     * @param UserInterface $user
     */
    public function add(UserInterface $user): void;

    /**
     * Get the current user in the collection.
     *
     * @return UserInterface
     */
    public function current(): UserInterface;

    /**
     * Get a user by offset.
     *
     * @param mixed $offset
     *
     * @return null|UserInterface
     */
    public function offsetGet(mixed $offset): ?UserInterface;

    /**
     * @param UsersShape $data
     */
    public static function fromArray(array $data): self;
}
