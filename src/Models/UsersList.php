<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\CollectionSchemaInterface;

/**
 * @extends IndexedCollection<UsersListUser>
 *
 * @implements UsersListInterface<UsersListUser>
 */
final class UsersList extends IndexedCollection implements UsersListInterface
{
    protected static string $itemType = UsersListUser::class;

    protected static ?CollectionSchemaInterface $schema = null;

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
