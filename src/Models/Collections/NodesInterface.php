<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\NodeInterface;
use Override;

/**
 * @template T of NodeInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface NodesInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{
     * name: string,
     * leaf?: array{users?: array<int, string>, computed?: array{userset: string}, tupleToUserset?: mixed},
     * difference?: array{base: array<mixed>, subtract: array<mixed>},
     * union?: array<mixed>,
     * intersection?: array<mixed>,
     * direct?: object,
     * }>
     */
    #[Override]
    public function jsonSerialize(): array;
}
