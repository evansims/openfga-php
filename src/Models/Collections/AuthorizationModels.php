<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, ModelInterface};
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * Collection implementation for OpenFGA authorization model objects.
 *
 * This class provides a concrete implementation for managing collections of
 * authorization model objects. Authorization models define the relationship
 * structure, permissions, and conditions within an OpenFGA store, serving
 * as the schema for authorization decisions.
 *
 * @extends IndexedCollection<AuthorizationModelInterface>
 *
 * @implements AuthorizationModelsInterface<AuthorizationModelInterface>
 */
final class AuthorizationModels extends IndexedCollection implements AuthorizationModelsInterface
{
    /**
     * @phpstan-var class-string<AuthorizationModelInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = AuthorizationModel::class;

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
            wrapperKey: 'authorization_models',
        );
    }
}
