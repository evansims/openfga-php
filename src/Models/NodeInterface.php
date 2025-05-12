<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface NodeInterface extends ModelInterface
{
    public function getDifference(): ?UsersetTreeDifferenceInterface;

    public function getIntersection(): ?self;

    public function getLeaf(): ?LeafInterface;

    public function getName(): string;

    public function getUnion(): ?self;
}
