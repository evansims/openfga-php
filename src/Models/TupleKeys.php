<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<TupleKey>
 */
final class TupleKeys extends AbstractIndexedCollection implements TupleKeysInterface
{
    protected static string $itemType = TupleKey::class;

    /**
     * @return TupleKeyInterface
     */
    public function current(): TupleKeyInterface
    {
        return parent::current();
    }

    /**
     * @param mixed $offset
     *
     * @return null|TupleKeyInterface
     */
    public function offsetGet(mixed $offset): ?TupleKeyInterface
    {
        $result = parent::offsetGet($offset);

        return $result instanceof TupleKeyInterface ? $result : null;
    }
}
