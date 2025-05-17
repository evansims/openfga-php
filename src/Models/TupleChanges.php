<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<TupleChange>
 */
final class TupleChanges extends AbstractIndexedCollection implements TupleChangesInterface
{
    protected static string $itemType = TupleChange::class;

    /**
     * @return null|TupleChangeInterface
     */
    public function current(): ?TupleChangeInterface
    {
        /** @var null|TupleChangeInterface $result */
        return parent::current();
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
