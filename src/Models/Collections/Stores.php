<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{Store, StoreInterface};

use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends IndexedCollection<StoreInterface>
 *
 * @implements StoresInterface<StoreInterface>
 */
final class Stores extends IndexedCollection implements StoresInterface
{
    private static ?CollectionSchemaInterface $schema = null;

    protected static string $itemType = Store::class;

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): CollectionSchemaInterface
    {
        return self::$schema ??= new CollectionSchema(
            className: static::class,
            itemType: static::$itemType,
            requireItems: false,
            wrapperKey: 'stores',
        );
    }
}
