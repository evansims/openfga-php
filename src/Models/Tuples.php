<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @implements \ArrayAccess<int, TupleInterface>
 * @implements \Iterator<int, TupleInterface>
 */
final class Tuples extends AbstractIndexedCollection implements TuplesInterface
{
    /**
     * @var class-string<TupleInterface>
     */
    protected static string $itemType = Tuple::class;

    /**
     * @param iterable<TupleInterface>|TupleInterface ...$tuples
     */
    public function __construct(iterable | TupleInterface ...$tuples)
    {
        parent::__construct(...$tuples);
    }
}
