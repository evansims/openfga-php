<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\CollectionSchemaInterface;

/**
 * @extends AbstractIndexedCollection<Node>
 */
final class Nodes extends AbstractIndexedCollection implements NodesInterface
{
    protected static string $itemType = Node::class;

    protected static ?CollectionSchemaInterface $schema = null;

    /**
     * @return null|NodeInterface
     */
    public function current(): ?NodeInterface
    {
        /** @var null|NodeInterface $result */
        return parent::current();
    }

    /**
     * @param mixed $offset
     *
     * @return null|NodeInterface
     */
    public function offsetGet(mixed $offset): ?NodeInterface
    {
        /** @var null|NodeInterface $result */
        $result = parent::offsetGet($offset);

        return $result instanceof NodeInterface ? $result : null;
    }
}
