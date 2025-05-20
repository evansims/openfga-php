<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\StoreInterface;

/**
 * @extends IndexedCollection<StoreInterface>
 *
 * @implements StoresInterface<StoreInterface>
 */
final class Stores extends IndexedCollection implements StoresInterface
{
    protected static string $itemType = StoreInterface::class;
}
