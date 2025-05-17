<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<Store>
 */
final class Stores extends AbstractIndexedCollection implements StoresInterface
{
    protected static string $itemType = Store::class;
}
