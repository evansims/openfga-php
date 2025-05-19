<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends IndexedCollection<NodeInterface>
 *
 * @implements NodesInterface<NodeInterface>
 */
final class Nodes extends IndexedCollection implements NodesInterface
{
    protected static string $itemType = Node::class;
}
