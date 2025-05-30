<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface};

use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends IndexedCollection<AuthorizationModelInterface>
 *
 * @implements AuthorizationModelsInterface<AuthorizationModelInterface>
 */
final class AuthorizationModels extends IndexedCollection implements AuthorizationModelsInterface
{
    private static ?CollectionSchemaInterface $schema = null;

    protected static string $itemType = AuthorizationModel::class;

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
            wrapperKey: 'authorization_models',
        );
    }
}
