<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use Override;

/**
 * @extends IndexedCollectionInterface<\OpenFGA\Models\TupleChangeInterface>
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
    #[Override]
    public function jsonSerialize(): array;
}
