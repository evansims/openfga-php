<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{TupleKey, TupleKeyInterface};

/**
 * @extends IndexedCollection<TupleKeyInterface>
 *
 * @implements TupleKeysInterface<TupleKeyInterface>
 */
final class TupleKeys extends IndexedCollection implements TupleKeysInterface
{
    protected static string $itemType = TupleKey::class;
}
