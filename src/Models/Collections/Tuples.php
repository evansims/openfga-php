<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{Tuple, TupleInterface};

use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends IndexedCollection<TupleInterface>
 *
 * @implements TuplesInterface<TupleInterface>
 */
final class Tuples extends IndexedCollection implements TuplesInterface
{
    private static ?CollectionSchemaInterface $schema = null;

    protected static string $itemType = Tuple::class;

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
            wrapperKey: 'tuples',
        );
    }
}
