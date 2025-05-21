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
        private readonly ?NodeInterface $union = null,
        private readonly ?NodeInterface $intersection = null,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getDifference(): ?UsersetTreeDifferenceInterface
    {
        return $this->difference;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getIntersection(): ?NodeInterface
    {
        return $this->intersection;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getLeaf(): ?LeafInterface
    {
        return $this->leaf;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getUnion(): ?NodeInterface
    {
        return $this->union;
    }

    #[Override]
    /**
     * @inheritDoc
     */
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

    #[Override]
    /**
     * @inheritDoc
     */
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'name', type: 'string', required: true),
                new SchemaProperty(name: 'leaf', type: Leaf::class, required: false),
                new SchemaProperty(name: 'difference', type: UsersetTreeDifference::class, required: false),
                new SchemaProperty(name: 'union', type: 'self', required: false, className: self::class),
                new SchemaProperty(name: 'intersection', type: 'self', required: false, className: self::class),
            ],
        );
    }
}
