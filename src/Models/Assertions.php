<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\CollectionSchemaInterface;

/**
 * @extends IndexedCollection<Assertion>
 *
 * @implements AssertionsInterface<Assertion>
 */
final class Assertions extends IndexedCollection implements AssertionsInterface
{
    protected static string $itemType = Assertion::class;

    protected static ?CollectionSchemaInterface $schema = null;
}
