<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{ModelInterface, TupleChange, TupleChangeInterface};
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends IndexedCollection<TupleChangeInterface>
 *
 * @implements TupleChangesInterface<TupleChangeInterface>
 */
final class TupleChanges extends IndexedCollection implements TupleChangesInterface
{
    /**
     * @phpstan-var class-string<TupleChangeInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = TupleChange::class;

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
            wrapperKey: 'changes',
        );
    }
}
