<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends IndexedCollection<UsersetInterface>
 *
 * @implements UsersetsInterface<UsersetInterface>
 */
final class Usersets extends IndexedCollection implements UsersetsInterface
{
    protected static string $itemType = Userset::class;
}
