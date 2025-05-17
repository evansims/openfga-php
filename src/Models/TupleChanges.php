<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<TupleChange>
 */
final class TupleChanges extends AbstractIndexedCollection implements TupleChangesInterface
{
    protected static string $itemType = TupleChange::class;
}
