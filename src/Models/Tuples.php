<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<Tuple>
 */
final class Tuples extends AbstractIndexedCollection implements TuplesInterface
{
    protected static string $itemType = Tuple::class;

    /**
     * @return null|TupleInterface
     */
    public function current(): ?TupleInterface
    {
        /** @var null|TupleInterface $result */
        return parent::current();
    }

    /**
     * @param mixed $offset
     *
     * @return null|TupleInterface
     */
    public function offsetGet(mixed $offset): ?TupleInterface
    {
        /** @var null|TupleInterface $result */
        $result = parent::offsetGet($offset);

        return $result instanceof TupleInterface ? $result : null;
    }
}
