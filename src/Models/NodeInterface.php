<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type NodeShape = array{name: string, leaf?: LeafShape, difference?: UsersetTreeDifferenceShape, union?: NodeShape, intersection?: NodeShape}
 */
interface NodeInterface extends ModelInterface
{
    public function getDifference(): ?UsersetTreeDifferenceInterface;

    public function getIntersection(): ?self;

    public function getLeaf(): ?LeafInterface;

    public function getName(): string;

    public function getUnion(): ?self;

    /**
     * @return NodeShape
     */
    public function jsonSerialize(): array;
}
