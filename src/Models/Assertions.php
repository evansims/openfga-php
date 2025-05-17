<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<Assertion>
 *
 * @implements AssertionsInterface<Assertion>
 */
final class Assertions extends AbstractIndexedCollection implements AssertionsInterface
{
    protected static string $itemType = Assertion::class;
}
