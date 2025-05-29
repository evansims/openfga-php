<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{TupleChange, TupleChangeInterface};

use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends IndexedCollection<TupleChangeInterface>
 *
 * @implements TupleChangesInterface<TupleChangeInterface>
 */
final class TupleChanges extends IndexedCollection implements TupleChangesInterface
{
    private static ?CollectionSchemaInterface $schema = null;

    protected static string $itemType = TupleChange::class;

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
            wrapperKey: 'changes',
        );
    }
}
