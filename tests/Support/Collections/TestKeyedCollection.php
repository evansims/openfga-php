<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Collections;

use OpenFGA\Models\Collections\KeyedCollection;
use OpenFGA\Models\TupleKey;

/**
 * Test concrete class extending KeyedCollection for unit testing purposes.
 */
final class TestKeyedCollection extends KeyedCollection
{
    protected static string $itemType = TupleKey::class;
}
