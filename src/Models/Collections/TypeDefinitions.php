<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{TypeDefinition, TypeDefinitionInterface};

use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends IndexedCollection<TypeDefinitionInterface>
 *
 * @implements TypeDefinitionsInterface<TypeDefinitionInterface>
 */
final class TypeDefinitions extends IndexedCollection implements TypeDefinitionsInterface
{
    private static ?CollectionSchemaInterface $schema = null;

    protected static string $itemType = TypeDefinition::class;

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
            wrapperKey: 'type_definitions',
        );
    }
}
