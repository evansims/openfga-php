<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of StoreInterface
 * @extends AbstractIndexedCollection<T>
 */
final class Stores extends AbstractIndexedCollection implements StoresInterface
{
    /**
     * @var class-string<T>
     */
    protected static string $itemType = Store::class;

    /**
     * @param list<T>|T ...$stores
     */
    public function __construct(iterable | StoreInterface ...$stores)
    {
        parent::__construct(...$stores);
    }
}
