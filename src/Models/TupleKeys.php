<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\CollectionSchemaInterface;

/**
 * @extends AbstractIndexedCollection<TupleKey>
 */
final class TupleKeys extends AbstractIndexedCollection implements TupleKeysInterface
{
    protected static string $itemType = TupleKey::class;

    protected static ?CollectionSchemaInterface $schema = null;

    /**
     * @return null|TupleKeyInterface
     */
    public function current(): ?TupleKeyInterface
    {
        /** @var null|TupleKeyInterface $result */
        return parent::current();
    }

    /**
     * @param mixed $offset
     *
     * @return null|TupleKeyInterface
     */
    public function offsetGet(mixed $offset): ?TupleKeyInterface
    {
        /** @var null|TupleKeyInterface $result */
        $result = parent::offsetGet($offset);

        return $result instanceof TupleKeyInterface ? $result : null;
    }
}
