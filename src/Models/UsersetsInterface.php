<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface UsersetsInterface extends CollectionInterface
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
     * @return null|UsersetInterface
     */
    public function current(): ?UsersetInterface;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function jsonSerialize(): array;

    /**
     * Get a userset by offset.
     *
     * @param mixed $offset
     *
     * @return null|UsersetInterface
     */
    public function offsetGet(mixed $offset): ?UsersetInterface;
}
