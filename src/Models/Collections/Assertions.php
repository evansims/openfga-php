<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\AssertionInterface;

/**
 * @extends IndexedCollection<AssertionInterface>
 *
 * @implements AssertionsInterface<AssertionInterface>
 */
final class Assertions extends IndexedCollection implements AssertionsInterface
{
    protected static string $itemType = AssertionInterface::class;
}
