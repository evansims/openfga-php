<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<Userset>
 */
final class Usersets extends AbstractIndexedCollection implements UsersetsInterface
{
    protected static string $itemType = Userset::class;
}
