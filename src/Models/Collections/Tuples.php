<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{ModelInterface, Tuple, TupleInterface};
use OpenFGA\Schemas\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * Collection implementation for OpenFGA tuple objects.
 *
 * This class provides a concrete implementation for managing collections of
 * tuple objects that represent relationship facts in the OpenFGA authorization
 * system. Tuples define the actual relationships between users, objects, and
 * relations that drive authorization decisions.
 *
 * Each tuple includes both the relationship key and a timestamp, making this
 * collection useful for both current state queries and historical analysis.
 *
 * @extends IndexedCollection<\OpenFGA\Models\TupleInterface>
 */
final class Tuples extends IndexedCollection implements TuplesInterface
{
    /**
     * @phpstan-var class-string<TupleInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = Tuple::class;

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
            wrapperKey: 'tuples',
        );
    }
}
