<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\StoreInterface;
use Override;

/**
 * @template T of StoreInterface
 *
 * @extends IndexedCollectionInterface<T>
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
