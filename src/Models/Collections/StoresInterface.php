<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\StoreInterface;

/**
 * @template T of StoreInterface
 * @extends IndexedCollectionInterface<T>
 */
interface StoresInterface extends IndexedCollectionInterface
{
    /**
     * Add a store to the collection.
     *
     * @param T $store
     */
    public function add(StoreInterface $store): void;

    /**
     * Get the current store in the collection.
     *
     * @return T
     */
    public function current(): StoreInterface;

    /**
     * @return array<int, array{
     *     id: string,
     *     name: string,
     *     created_at: string,
     *     updated_at: string,
     *     deleted_at?: string,
     * }>
     */
    public function jsonSerialize(): array;

    /**
     * Get a store by offset.
     *
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?StoreInterface;
}
