<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\UsersetInterface;

/**
 * @extends KeyedCollection<UsersetInterface>
 *
 * @implements UsersetsInterface<UsersetInterface>
 */
final class Usersets extends KeyedCollection implements UsersetsInterface
{
    protected static string $itemType = UsersetInterface::class;
}
