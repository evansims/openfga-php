<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of Userset
 *
 * @extends CollectionInterface<T>
 */
interface UsersetsInterface extends CollectionInterface
{
    /**
     * Add a userset to the collection.
     *
     * @param T $userset
     */
    public function add(UsersetInterface $userset): void;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function jsonSerialize(): array;
}
