<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{ModelInterface, Userset, UsersetInterface};
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends KeyedCollection<UsersetInterface>
 *
 * @implements TypeDefinitionRelationsInterface<UsersetInterface>
 */
final class TypeDefinitionRelations extends KeyedCollection implements TypeDefinitionRelationsInterface
{
    /**
     * @phpstan-var class-string<UsersetInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = Userset::class;

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
            wrapperKey: 'relations',
        );
    }
}
