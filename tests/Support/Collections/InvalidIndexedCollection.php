<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Collections;

use OpenFGA\Models\Collections\IndexedCollection;

/**
 * Test class without $itemType defined for unit testing purposes.
 */
final class InvalidIndexedCollection extends IndexedCollection
{
    // Missing $itemType property intentionally for testing
}
