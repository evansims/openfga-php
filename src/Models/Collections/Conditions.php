<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{Condition, ConditionInterface};

use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends IndexedCollection<ConditionInterface>
 *
 * @implements ConditionsInterface<ConditionInterface>
 */
final class Conditions extends IndexedCollection implements ConditionsInterface
{
    private static ?CollectionSchemaInterface $schema = null;

    protected static string $itemType = Condition::class;

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
            wrapperKey: 'conditions',
        );
    }
}
