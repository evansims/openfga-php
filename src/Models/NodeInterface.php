<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface NodeInterface extends ModelInterface
{
    public function getDifference(): ?UsersetTreeDifferenceInterface;

    public function getIntersection(): ?self;

    public function getLeaf(): ?LeafInterface;

    public function getName(): string;

    public function getUnion(): ?self;

    /**
     * @return array{name: string, leaf?: array{users?: array<int, string>, computed?: array{userset: string}, tupleToUserset?: mixed}, difference?: mixed, intersection?: mixed, union?: mixed}
     */
    #[Override]
    public function jsonSerialize(): array;
}
