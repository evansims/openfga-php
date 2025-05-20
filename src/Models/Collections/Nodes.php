<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\NodeInterface;

/**
 * @extends IndexedCollection<NodeInterface>
 *
 * @implements NodesInterface<NodeInterface>
 */
final class Nodes extends IndexedCollection implements NodesInterface
{
    protected static string $itemType = NodeInterface::class;
}
