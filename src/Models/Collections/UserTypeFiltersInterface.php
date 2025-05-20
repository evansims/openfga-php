<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\UserTypeFilterInterface;

/**
 * @template T of UserTypeFilterInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface UserTypeFiltersInterface extends IndexedCollectionInterface
{
    /**
     * Add a user type filter to the collection.
     *
     * @param UserTypeFilterInterface $userTypeFilter
     */
    public function add(UserTypeFilterInterface $userTypeFilter): void;

    /**
     * Get the current user type filter in the collection.
     *
     * @return UserTypeFilterInterface
     */
    public function current(): UserTypeFilterInterface;

    /**
     * @return array<int, array{type: string, relation?: string}>
     */
    public function jsonSerialize(): array;

    /**
     * Get a user type filter by offset.
     *
     * @param mixed $offset
     *
     * @return null|UserTypeFilterInterface
     */
    public function offsetGet(mixed $offset): ?UserTypeFilterInterface;
}
