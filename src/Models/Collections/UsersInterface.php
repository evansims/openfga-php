<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\UserInterface;

/**
 * @template T of UserInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface UsersInterface extends IndexedCollectionInterface
{
    /**
     * Add a user to the collection.
     *
     * @param T $user
     */
    public function add(UserInterface $user): void;

    /**
     * Get the current user in the collection.
     *
     * @return T
     */
    public function current(): UserInterface;

    /**
     * @return array<int, array{object?: mixed, userset?: array{type: string, id: string, relation: string}, wildcard?: array{type: string}}>
     */
    public function jsonSerialize(): array;

    /**
     * Get a user by offset.
     *
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?UserInterface;
}
