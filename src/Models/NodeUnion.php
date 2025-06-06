<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents a union of multiple nodes in an authorization model tree.
 *
 * When OpenFGA evaluates complex authorization rules, it often needs to combine
 * results from multiple authorization paths. A NodeUnion contains a collection
 * of nodes that should be evaluated together, typically representing an OR
 * relationship where access is granted if any of the contained nodes grants access.
 *
 * This is commonly used in authorization model structures where a user can
 * have access through multiple different permission paths.
 */
final class NodeUnion implements NodeUnionInterface
{
    public const string OPENAPI_MODEL = 'NodeUnion';

    private static ?SchemaInterface $schema = null;

    /**
     * @param array<int, NodeInterface> $nodes Array of node interfaces for the union
     */
    public function __construct(
        private readonly array $nodes,
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
                new SchemaProperty(name: 'nodes', type: 'array', required: true, items: ['type' => 'object', 'className' => Node::class]),
            ],
        );
    }

    /**
     * @inheritDoc
     *
     * @return array<int, NodeInterface>
     */
    #[Override]
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'nodes' => array_map(
                static fn (NodeInterface $node): array => $node->jsonSerialize(),
                $this->nodes,
            ),
        ];
    }
}
