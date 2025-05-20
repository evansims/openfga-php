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

    /**
     * @return array{
     * name: string,
     * leaf?: array{users?: array<int, string>, computed?: array{userset: string}, tupleToUserset?: mixed},
     * difference?: array{base: array<mixed>, subtract: array<mixed>},
     * union?: array<mixed>,
     * intersection?: array<mixed>,
     * direct?: object,
     * }
     */
    public function jsonSerialize(): array;
}
