<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Collections;

use OpenFGA\Models\Collections\IndexedCollection;
use OpenFGA\Models\TupleKey;

/**
 * Test concrete class extending IndexedCollection for unit testing purposes.
 */
final class TestIndexedCollection extends IndexedCollection
{
    protected static string $itemType = TupleKey::class;
}
