<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{User, UserInterface};

/**
 * @extends IndexedCollection<UserInterface>
 *
 * @implements UsersInterface<UserInterface>
 */
final class Users extends IndexedCollection implements UsersInterface
{
    protected static string $itemType = User::class;
}
