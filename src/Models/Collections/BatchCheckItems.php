<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{BatchCheckItem, BatchCheckItemInterface, ModelInterface};
use OpenFGA\Schemas\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * Collection of batch check items for batch authorization requests.
 *
 * This collection maintains a list of BatchCheckItem objects, each representing
 * a single authorization check to be performed as part of a batch request.
 *
 * @extends IndexedCollection<\OpenFGA\Models\BatchCheckItemInterface>
 *
 * @see BatchCheckItemsInterface For the complete API specification
 * @see BatchCheckItemInterface
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/BatchCheck
 */
final class BatchCheckItems extends IndexedCollection implements BatchCheckItemsInterface
{
    /**
     * @phpstan-var class-string<BatchCheckItemInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = BatchCheckItem::class;

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
