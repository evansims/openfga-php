<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\TupleInterface;

/**
 * @template T of TupleInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface TuplesInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{key: array{user: string, relation: string, object: string, condition?: array<string, mixed>}, timestamp: string}>
     */
    public function jsonSerialize(): array;
}
