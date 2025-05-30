<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{Assertion, AssertionInterface, ModelInterface};
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * Collection implementation for OpenFGA assertion models.
 *
 * This class provides a concrete implementation for managing collections of
 * assertion objects used in testing authorization model correctness. Assertions
 * define expected authorization outcomes for specific tuple configurations and
 * are essential for validating model behavior.
 *
 * @extends IndexedCollection<AssertionInterface>
 *
 * @implements AssertionsInterface<AssertionInterface>
 */
final class Assertions extends IndexedCollection implements AssertionsInterface
{
    /**
     * @phpstan-var class-string<AssertionInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = Assertion::class;

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
        );
    }
}
