<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @implements \ArrayAccess<int, TupleKeyInterface>
 * @implements \Iterator<int, TupleKeyInterface>
 */
final class TupleKeys extends AbstractIndexedCollection implements TupleKeysInterface
{
    /**
     * @var class-string<TupleKeyInterface>
     */
    protected static string $itemType = TupleKey::class;

    /**
     * @param iterable<TupleKeyInterface>|TupleKeyInterface ...$tupleKeys
     */
    public function __construct(iterable | TupleKeyInterface ...$tupleKeys)
    {
        parent::__construct(...$tupleKeys);
    }
}
