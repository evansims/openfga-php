<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends IndexedCollection<AssertionInterface>
 *
 * @implements AssertionsInterface<AssertionInterface>
 */
final class Assertions extends IndexedCollection implements AssertionsInterface
{
    protected static string $itemType = Assertion::class;
}
