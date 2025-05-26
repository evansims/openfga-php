<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{TupleChangeInterface, TupleChange};

/**
 * @extends IndexedCollection<TupleChangeInterface>
 *
 * @implements TupleChangesInterface<TupleChangeInterface>
 */
final class TupleChanges extends IndexedCollection implements TupleChangesInterface
{
    protected static string $itemType = TupleChange::class;
}
