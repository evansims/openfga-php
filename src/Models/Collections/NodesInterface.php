<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\NodeInterface;

/**
 * @template T of NodeInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface NodesInterface extends IndexedCollectionInterface
{
    /**
     * Add a node to the collection.
     *
     * @param T $node
     */
    public function add(NodeInterface $node): void;

    /**
     * Get the current node in the collection.
     *
     * @return T
     */
    public function current(): NodeInterface;

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
    public function jsonSerialize(): array;

    /**
     * Get a node by offset.
     *
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?NodeInterface;
}
