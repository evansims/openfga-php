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
        return array_filter([
            'name' => $this->name,
            'leaf' => $this->leaf?->jsonSerialize(),
            'difference' => $this->difference?->jsonSerialize(),
            'union' => $this->union?->jsonSerialize(),
            'intersection' => $this->intersection?->jsonSerialize(),
        ], static fn ($value) => null !== $value);
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
