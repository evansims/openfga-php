<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{Assertion, AssertionInterface};
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends IndexedCollection<AssertionInterface>
 *
 * @implements AssertionsInterface<AssertionInterface>
 */
final class Assertions extends IndexedCollection implements AssertionsInterface
{
    private static ?CollectionSchemaInterface $schema = null;

    protected static string $itemType = Assertion::class;

    #[Override]
    public static function schema(): CollectionSchemaInterface
    {
        return self::$schema ??= new CollectionSchema(
            className: static::class,
            itemType: static::$itemType,
            requireItems: false,
        );
    }
}
