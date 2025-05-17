<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<UsersListUser>
 *
 * @implements UsersListInterface<UsersListUser>
 */
final class UsersList extends AbstractIndexedCollection implements UsersListInterface
{
    protected static string $itemType = UsersListUser::class;

    /**
     * @return null|UsersListUserInterface
     */
    public function current(): ?UsersListUserInterface
    {
        /** @var null|UsersListUserInterface $result */
        return parent::current();
    }

    /**
     * @param mixed $offset
     *
     * @return null|UsersListUserInterface
     */
    public function offsetGet(mixed $offset): ?UsersListUserInterface
    {
        /** @var null|UsersListUserInterface $result */
        return parent::offsetGet($offset);
    }
}
