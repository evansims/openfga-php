<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type StoresShape = list<StoreShape>
 */
interface StoresInterface extends CollectionInterface
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
     * @return StoreInterface
     */
    public function current(): StoreInterface;

    /**
     * Get a store by offset.
     *
     * @param mixed $offset
     *
     * @return null|StoreInterface
     */
    public function offsetGet(mixed $offset): ?StoreInterface;

    /**
     * Create a collection from an array.
     *
     * @param StoresShape $data
     */
    public static function fromArray(array $data): self;
}
