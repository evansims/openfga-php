<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{Tuple, TupleInterface};

/**
 * @extends IndexedCollection<TupleInterface>
 *
 * @implements TuplesInterface<TupleInterface>
 */
final class Tuples extends IndexedCollection implements TuplesInterface
{
    protected static string $itemType = Tuple::class;
}
