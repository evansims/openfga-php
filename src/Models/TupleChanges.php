<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\CollectionSchemaInterface;

/**
 * @extends IndexedCollection<TupleChange>
 */
final class TupleChanges extends IndexedCollection implements TupleChangesInterface
{
    protected static string $itemType = TupleChange::class;

    protected static ?CollectionSchemaInterface $schema = null;

    /**
     * @return null|TupleChangeInterface
     */
    public function current(): ?TupleChangeInterface
    {
        /** @var null|TupleChangeInterface $result */
        return parent::current();
    }

    /**
     * @param null|(callable(TupleChangeInterface): bool) $callback
     *
     * @return null|TupleChangeInterface
     */
    public function first(?callable $callback = null): ?TupleChangeInterface
    {
        /** @var null|TupleChangeInterface $result */
        $result = parent::first($callback);

        return $result instanceof TupleChangeInterface ? $result : null;
    }

    /**
     * @param mixed $offset
     *
     * @return null|TupleChangeInterface
     */
    public function offsetGet(mixed $offset): ?TupleChangeInterface
    {
        /** @var null|TupleChangeInterface $result */
        $result = parent::offsetGet($offset);

        return $result instanceof TupleChangeInterface ? $result : null;
    }
}
