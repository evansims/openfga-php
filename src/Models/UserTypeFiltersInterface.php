<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type UserTypeFiltersShape = list<UserTypeFilterShape>
 *
 * @implements \ArrayAccess<int, UserTypeFilterInterface>
 * @implements \Iterator<int, UserTypeFilterInterface>
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
     * @return null|UserTypeFilterInterface
     */
    public function current(): ?UserTypeFilterInterface;

    /**
     * @return UserTypeFiltersShape
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
