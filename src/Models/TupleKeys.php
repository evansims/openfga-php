<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<TupleKey>
 */
final class TupleKeys extends AbstractIndexedCollection implements TupleKeysInterface
{
    protected static string $itemType = TupleKey::class;
}
