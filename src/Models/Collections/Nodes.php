<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{Node, NodeInterface};

use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends IndexedCollection<NodeInterface>
 *
 * @implements NodesInterface<NodeInterface>
 */
final class Nodes extends IndexedCollection implements NodesInterface
{
    private static ?CollectionSchemaInterface $schema = null;

    protected static string $itemType = Node::class;

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): CollectionSchemaInterface
    {
        return self::$schema ??= new CollectionSchema(
            className: static::class,
            itemType: static::$itemType,
            requireItems: false,
            wrapperKey: 'nodes',
        );
    }
}
