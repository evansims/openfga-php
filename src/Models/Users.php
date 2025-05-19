<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends IndexedCollection<UserInterface>
 *
 * @implements UsersInterface<UserInterface>
 */
final class Users extends IndexedCollection implements UsersInterface
{
    protected static string $itemType = User::class;
}
