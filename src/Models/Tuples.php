<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of TupleInterface
 * @extends AbstractIndexedCollection<T>
 */
final class Tuples extends AbstractIndexedCollection implements TuplesInterface
{
    /**
     * @var class-string<T>
     */
    protected static string $itemType = Tuple::class;

    /**
     * @param list<T>|T ...$tuples
     */
    public function __construct(iterable | TupleInterface ...$tuples)
    {
        parent::__construct(...$tuples);
    }
}
