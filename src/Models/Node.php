<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class Node implements NodeInterface
{
    public const OPENAPI_MODEL = 'Node';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $name,
        private readonly ?LeafInterface $leaf = null,
        private readonly ?UsersetTreeDifferenceInterface $difference = null,
        private readonly null | NodeInterface | NodeUnionInterface $union = null,
        private readonly null | NodeInterface | NodeUnionInterface $intersection = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getDifference(): ?UsersetTreeDifferenceInterface
    {
        return $this->difference;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getIntersection(): null | NodeInterface | NodeUnionInterface
    {
        return $this->intersection;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getLeaf(): ?LeafInterface
    {
        return $this->leaf;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getUnion(): null | NodeInterface | NodeUnionInterface
    {
        return $this->union;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return array_filter([
            'name' => $this->name,
            'leaf' => $this->leaf?->jsonSerialize(),
            'difference' => $this->difference?->jsonSerialize(),
            'union' => $this->union?->jsonSerialize(),
            'intersection' => $this->intersection?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'name', type: 'string', required: true),
                new SchemaProperty(name: 'leaf', type: 'object', className: Leaf::class, required: false),
                new SchemaProperty(name: 'difference', type: 'object', className: UsersetTreeDifference::class, required: false),
                new SchemaProperty(name: 'union', type: 'object', required: false, className: NodeUnion::class),
                new SchemaProperty(name: 'intersection', type: 'object', required: false, className: NodeUnion::class),
            ],
        );
    }
}
