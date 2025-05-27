<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{UsersListUser, UsersListUserInterface};

/**
 * @extends IndexedCollection<UsersListUserInterface>
 *
 * @implements UsersListInterface<UsersListUserInterface>
 */
final class UsersList extends IndexedCollection implements UsersListInterface
{
    protected static string $itemType = UsersListUser::class;
}
