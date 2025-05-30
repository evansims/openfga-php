<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Collections;

use OpenFGA\Models\Collections\KeyedCollection;
use stdClass;

/**
 * Test class with invalid $itemType for unit testing purposes.
 */
final class InvalidTypeKeyedCollection extends KeyedCollection
{
    protected static string $itemType = stdClass::class;
}
