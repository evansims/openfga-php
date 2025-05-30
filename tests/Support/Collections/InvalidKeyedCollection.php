<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Collections;

use OpenFGA\Models\Collections\KeyedCollection;

/**
 * Test class without $itemType defined for unit testing purposes.
 */
final class InvalidKeyedCollection extends KeyedCollection
{
    // Missing $itemType property intentionally for testing
}
