<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{Assertion, AssertionInterface, ModelInterface};
use OpenFGA\Schemas\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * Collection implementation for OpenFGA assertion models.
 *
 * @extends IndexedCollection<\OpenFGA\Models\AssertionInterface>
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
