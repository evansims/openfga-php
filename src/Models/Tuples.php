<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\CollectionSchemaInterface;

/**
 * @extends AbstractIndexedCollection<Tuple>
 */
final class Tuples extends AbstractIndexedCollection implements TuplesInterface
{
    protected static string $itemType = Tuple::class;

    protected static ?CollectionSchemaInterface $schema = null;

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
