<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\StoreInterface;
use Override;

/**
 * Collection interface for OpenFGA store objects.
 *
 * This interface defines a collection that holds store objects representing
 * individual OpenFGA authorization stores. Each store is an isolated
 * authorization domain with its own models, tuples, and configuration,
 * providing multi-tenancy within the OpenFGA system.
 *
 * @template T of StoreInterface
 *
 * @extends IndexedCollectionInterface<T>
 *
 * @see https://openfga.dev/docs/concepts#stores OpenFGA Stores
 */
interface StoresInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{
     *     id: string,
     *     name: string,
     *     created_at: string,
     *     updated_at: string,
     *     deleted_at?: string,
     * }>
     */
    #[Override]
    public function jsonSerialize(): array;
}
