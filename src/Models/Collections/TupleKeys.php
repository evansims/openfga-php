<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\TupleKeyInterface;

/**
 * @extends IndexedCollection<TupleKeyInterface>
 *
 * @implements TupleKeysInterface<TupleKeyInterface>
 */
final class TupleKeys extends IndexedCollection implements TupleKeysInterface
{
    protected static string $itemType = TupleKeyInterface::class;
}
