<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{User, UserInterface};

use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends IndexedCollection<UserInterface>
 *
 * @implements UsersInterface<UserInterface>
 */
final class Users extends IndexedCollection implements UsersInterface
{
    private static ?CollectionSchemaInterface $schema = null;

    protected static string $itemType = User::class;

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
            wrapperKey: 'users',
        );
    }
}
