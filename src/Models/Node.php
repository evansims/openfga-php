<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents a node in the authorization evaluation tree structure.
 *
 * When OpenFGA evaluates complex authorization rules, it builds a tree of nodes
 * representing different evaluation paths. Each node can contain unions, intersections,
 * differences, or leaf computations that contribute to the final authorization decision.
 *
 * This is the fundamental building block for representing how authorization
 * decisions are computed and provides insight into the evaluation process.
 */
final class Node implements NodeInterface
{
    public const string OPENAPI_MODEL = 'Node';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string                                $name         The name of the node
     * @param LeafInterface|null                    $leaf         Optional leaf node
     * @param UsersetTreeDifferenceInterface|null   $difference   Optional difference operation
     * @param NodeInterface|NodeUnionInterface|null $union        Optional union operation
     * @param NodeInterface|NodeUnionInterface|null $intersection Optional intersection operation
     */
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
}
