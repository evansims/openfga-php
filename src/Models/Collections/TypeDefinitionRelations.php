<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{Userset, UsersetInterface};

use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends KeyedCollection<UsersetInterface>
 *
 * @implements TypeDefinitionRelationsInterface<UsersetInterface>
 */
final class TypeDefinitionRelations extends KeyedCollection implements TypeDefinitionRelationsInterface
{
    private static ?CollectionSchemaInterface $schema = null;

    protected static string $itemType = Userset::class;

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
            wrapperKey: 'relations',
        );
    }
}
