<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class Node implements NodeInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private string $name,
        private ?LeafInterface $leaf = null,
        private ?UsersetTreeDifferenceInterface $difference = null,
        private ?NodeInterface $union = null,
        private ?NodeInterface $intersection = null,
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

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'name', type: 'string', required: true),
                new SchemaProperty(name: 'leaf', type: Leaf::class, required: false),
                new SchemaProperty(name: 'difference', type: UsersetTreeDifference::class, required: false),
                new SchemaProperty(name: 'union', type: self::class, required: false),
                new SchemaProperty(name: 'intersection', type: self::class, required: false),
            ],
        );
    }
}
