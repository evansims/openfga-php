<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type NodesShape = list<NodeShape>
 */
interface NodesInterface extends IndexedCollectionInterface
{
    /**
     * Add a node to the collection.
     *
     * @param NodeInterface $node
     */
    public function add(NodeInterface $node): void;

    /**
     * Get the current node in the collection.
     *
     * @return null|NodeInterface
     */
    public function current(): ?NodeInterface;

    /**
     * @return NodesShape
     */
    public function jsonSerialize(): array;

    /**
     * Get a node by offset.
     *
     * @param mixed $offset
     *
     * @return null|NodeInterface
     */
    public function offsetGet(mixed $offset): ?NodeInterface;
}
