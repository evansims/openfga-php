<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{ModelInterface, Node, NodeInterface};
use OpenFGA\Schemas\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends IndexedCollection<\OpenFGA\Models\NodeInterface>
 */
final class Nodes extends IndexedCollection implements NodesInterface
{
    /**
     * @phpstan-var class-string<NodeInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = Node::class;

    private static ?CollectionSchemaInterface $schema = null;

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): CollectionSchemaInterface
    {
        return self::$schema ??= new CollectionSchema(
            className: self::class,
            itemType: /** @var class-string */ self::$itemType,
            requireItems: false,
            wrapperKey: 'nodes',
        );
    }
}
