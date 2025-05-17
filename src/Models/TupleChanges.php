<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @implements \ArrayAccess<int, TupleChangeInterface>
 * @implements \Iterator<int, TupleChangeInterface>
 */
final class TupleChanges extends AbstractIndexedCollection implements TupleChangesInterface
{
    /**
     * @var class-string<TupleChangeInterface>
     */
    protected static string $itemType = TupleChange::class;

    /**
     * @param iterable<TupleChangeInterface>|TupleChangeInterface ...$changes
     */
    public function __construct(iterable | TupleChangeInterface ...$changes)
    {
        parent::__construct(...$changes);
    }
}
