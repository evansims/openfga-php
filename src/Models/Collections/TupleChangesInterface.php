<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\TupleChangeInterface;

/**
 * @template T of TupleChangeInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface TupleChangesInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{
     *     tuple_key: array{
     *         user: string,
     *         relation: string,
     *         object: string,
     *         condition?: array<string, mixed>,
     *     },
     *     operation: string,
     *     timestamp: string,
     * }>
     */
    public function jsonSerialize(): array;
}
