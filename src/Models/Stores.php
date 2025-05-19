<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends IndexedCollection<StoreInterface>
 *
 * @implements StoresInterface<StoreInterface>
 */
final class Stores extends IndexedCollection implements StoresInterface
{
    protected static string $itemType = Store::class;
}
