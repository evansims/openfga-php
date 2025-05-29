<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class NodeUnion implements NodeUnionInterface
{
    public const OPENAPI_TYPE = 'NodeUnion';

    private static ?SchemaInterface $schema = null;

    /**
     * @param array<int, NodeInterface> $nodes
     */
    public function __construct(
        private readonly array $nodes,
    ) {
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
            'nodes' => array_map(static fn (NodeInterface $node): array => $node->jsonSerialize(), $this->nodes),
        ];
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
}
