<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use function assert;

final class Node extends Model implements NodeInterface
{
    public function __construct(
        private string $name,
        private ?LeafInterface $leaf,
        private ?UsersetTreeDifferenceInterface $difference,
        private ?NodeInterface $union,
        private ?NodeInterface $intersection,
    ) {
    }

    public function getDifference(): ?UsersetTreeDifferenceInterface
    {
        return $this->difference;
    }

    public function getIntersection(): ?NodeInterface
    {
        return $this->intersection;
    }

    public function getLeaf(): ?LeafInterface
    {
        return $this->leaf;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUnion(): ?NodeInterface
    {
        return $this->union;
    }

    public function toArray(): array
    {
        $arr = [];

        if ($this->leaf) {
            $arr['leaf'] = $this->leaf->toArray();
        }

        if ($this->difference) {
            $arr['difference'] = $this->difference->toArray();
        }

        if ($this->union) {
            $arr['union'] = $this->union->toArray();
        }

        if ($this->intersection) {
            $arr['intersection'] = $this->intersection->toArray();
        }

        return [
            'name' => $this->name,
            ...$arr,
        ];
    }

    public static function fromArray(array $data): self
    {
        assert(isset($data['name']));

        return new self(
            name: $data['name'],
            leaf: isset($data['leaf']) ? Leaf::fromArray($data['leaf']) : null,
            difference: isset($data['difference']) ? UsersetTreeDifference::fromArray($data['difference']) : null,
            union: isset($data['union']) ? self::fromArray($data['union']) : null,
            intersection: isset($data['intersection']) ? self::fromArray($data['intersection']) : null,
        );
    }
}
