<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of TupleChangeInterface
 * @extends AbstractIndexedCollection<T>
 */
final class TupleChanges extends AbstractIndexedCollection implements TupleChangesInterface
{
    /**
     * @var class-string<T>
     */
    protected static string $itemType = TupleChange::class;

    /**
     * @param list<T>|T ...$changes
     */
    public function __construct(iterable | TupleChangeInterface ...$changes)
    {
        parent::__construct(...$changes);
    }
}
