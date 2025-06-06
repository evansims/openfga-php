<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{ModelInterface, TypeDefinition, TypeDefinitionInterface};
use OpenFGA\Schemas\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * Collection implementation for OpenFGA type definition objects.
 *
 * This class provides a concrete implementation for managing collections of
 * type definition objects that specify object types, relations, and metadata
 * within an authorization model. Type definitions are fundamental building
 * blocks that define the schema for authorization relationships.
 *
 * @extends IndexedCollection<\OpenFGA\Models\TypeDefinitionInterface>
 */
final class TypeDefinitions extends IndexedCollection implements TypeDefinitionsInterface
{
    /**
     * @phpstan-var class-string<TypeDefinitionInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = TypeDefinition::class;

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
            wrapperKey: 'type_definitions',
        );
    }
}
