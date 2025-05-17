<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<User>
 */
final class Users extends AbstractIndexedCollection implements UsersInterface
{
    protected static string $itemType = User::class;
}
