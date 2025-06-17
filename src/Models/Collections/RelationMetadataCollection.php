<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{ModelInterface, RelationMetadata, RelationMetadataInterface};
use OpenFGA\Schemas\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * Collection implementation for OpenFGA relation metadata objects.
 *
 * This class provides a concrete implementation for managing keyed collections of
 * relation metadata objects. Relation metadata provides additional context about
 * relations defined in authorization model type definitions, including module
 * information and source file details.
 *
 * The collection uses relation names as keys, enabling efficient retrieval of
 * metadata for specific relations within a type definition.
 *
 * @extends KeyedCollection<\OpenFGA\Models\RelationMetadataInterface>
 */
final class RelationMetadataCollection extends KeyedCollection implements RelationMetadataCollectionInterface
{
    /**
     * @phpstan-var class-string<RelationMetadataInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = RelationMetadata::class;

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
