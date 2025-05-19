<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends IndexedCollection<UsersListUserInterface>
 *
 * @implements UsersListInterface<UsersListUserInterface>
 */
final class UsersList extends IndexedCollection implements UsersListInterface
{
    protected static string $itemType = UsersListUser::class;
}
