<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type StoresShape = list<StoreShape>
 *
 * @extends IndexedCollectionInterface<StoreInterface>
 *
 * @implements \ArrayAccess<int, StoreInterface>
 * @implements \Iterator<int, StoreInterface>
 */
interface StoresInterface extends IndexedCollectionInterface
{
    /**
     * Add a store to the collection.
     *
     * @param StoreInterface $store
     */
    public function add(StoreInterface $store): void;

    /**
     * Get the current store in the collection.
     *
     * @return null|StoreInterface
     */
    public function current(): ?StoreInterface;

    /**
     * Get a store by offset.
     *
     * @param mixed $offset
     *
     * @return null|StoreInterface
     */
    public function offsetGet(mixed $offset): ?StoreInterface;
}
