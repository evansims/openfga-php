<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Collections;

use OpenFGA\Models\Collections\IndexedCollection;
use stdClass;

/**
 * Test class with invalid $itemType for unit testing purposes.
 */
final class InvalidTypeIndexedCollection extends IndexedCollection
{
    protected static string $itemType = stdClass::class;
}
