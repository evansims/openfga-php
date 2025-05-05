<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface UsersetsInterface extends ModelCollectionInterface
{
    /**
     * Add a userset to the collection.
     *
     * @param UsersetInterface $userset
     */
    public function add(UsersetInterface $userset): void;

    /**
     * Get the current userset in the collection.
     *
     * @return UsersetInterface
     */
    public function current(): UsersetInterface;

    /**
     * Get a userset by offset.
     *
     * @param mixed $offset
     *
     * @return null|UsersetInterface
     */
    public function offsetGet(mixed $offset): ?UsersetInterface;
}
