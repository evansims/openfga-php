<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of TupleKeyInterface
 * @extends AbstractIndexedCollection<T>
 */
final class TupleKeys extends AbstractIndexedCollection implements TupleKeysInterface
{
    /**
     * @var class-string<T>
     */
    protected static string $itemType = TupleKey::class;

    /**
     * @param list<T>|T ...$tupleKeys
     */
    public function __construct(iterable | TupleKeyInterface ...$tupleKeys)
    {
        parent::__construct(...$tupleKeys);
    }
}
