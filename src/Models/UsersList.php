<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<UsersListUser>
 */
final class UsersList extends AbstractIndexedCollection implements UsersListInterface
{
    protected static string $itemType = UsersListUser::class;
}
