<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{Condition, ConditionInterface, ModelInterface};
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * Collection implementation for OpenFGA condition objects.
 *
 * This class provides a concrete implementation for managing collections of
 * condition objects that enable context-aware authorization decisions in
 * OpenFGA authorization models. Conditions allow for dynamic access control
 * based on runtime attributes and environmental factors.
 *
 * Each condition in the collection defines an expression, parameters, and
 * metadata that determine how authorization decisions should be evaluated
 * when specific contextual criteria are met.
 *
 * @extends IndexedCollection<ConditionInterface>
 *
 * @implements ConditionsInterface<ConditionInterface>
 */
final class Conditions extends IndexedCollection implements ConditionsInterface
{
    /**
     * @phpstan-var class-string<ConditionInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = Condition::class;

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
            wrapperKey: 'conditions',
        );
    }
}
