<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{ModelInterface, Store, StoreInterface};
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * Collection implementation for OpenFGA store objects.
 *
 * This class provides a concrete implementation for managing collections of
 * store objects that represent individual OpenFGA authorization stores.
 * Each store provides an isolated authorization domain with its own models,
 * tuples, and configuration for multi-tenant authorization systems.
 *
 * @extends IndexedCollection<StoreInterface>
 *
 * @implements StoresInterface<StoreInterface>
 */
final class Stores extends IndexedCollection implements StoresInterface
{
    /**
     * @phpstan-var class-string<StoreInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = Store::class;

    private static ?CollectionSchemaInterface $schema = null;

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): CollectionSchemaInterface
    {
        return self::$schema ??= new CollectionSchema(
            className: self::class,
            itemType: /** @var class-string */ self::$itemType,
            requireItems: false,
            wrapperKey: 'stores',
        );
    }
}
