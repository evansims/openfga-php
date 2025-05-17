<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @implements \ArrayAccess<int, StoreInterface>
 * @implements \Iterator<int, StoreInterface>
 */
final class Stores extends AbstractIndexedCollection implements StoresInterface
{
    /**
     * @var class-string<StoreInterface>
     */
    protected static string $itemType = Store::class;

    /**
     * @param iterable<StoreInterface>|StoreInterface ...$stores
     */
    public function __construct(iterable | StoreInterface ...$stores)
    {
        parent::__construct(...$stores);
    }
}
