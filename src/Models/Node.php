<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class Node implements NodeInterface
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

    public function jsonSerialize(): array
    {
        $response = [
            'name' => $this->getName(),
        ];

        if ($this->getLeaf()) {
            $response['leaf'] = $this->getLeaf()->jsonSerialize();
        }

        if ($this->getDifference()) {
            $response['difference'] = $this->getDifference()->jsonSerialize();
        }

        if ($this->getUnion()) {
            $response['union'] = $this->getUnion()->jsonSerialize();
        }

        if ($this->getIntersection()) {
            $response['intersection'] = $this->getIntersection()->jsonSerialize();
        }

        return $response;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedNodeShape($data);

        return new self(
            name: $data['name'],
            leaf: isset($data['leaf']) ? Leaf::fromArray($data['leaf']) : null,
            difference: isset($data['difference']) ? UsersetTreeDifference::fromArray($data['difference']) : null,
            union: isset($data['union']) ? self::fromArray($data['union']) : null,
            intersection: isset($data['intersection']) ? self::fromArray($data['intersection']) : null,
        );
    }

    /**
     * Validates the shape of the array to be used as node data. Throws an exception if the data is invalid.
     *
     * @param array{name: string, leaf?: LeafShape, difference?: UsersetTreeDifferenceShape, union?: NodeShape, intersection?: NodeShape} $data
     *
     * @throws InvalidArgumentException
     *
     * @return NodeShape
     */
    public static function validatedNodeShape(array $data): array
    {
        if (! isset($data['name'])) {
            throw new InvalidArgumentException('Node must have a name');
        }

        return $data;
    }
}
