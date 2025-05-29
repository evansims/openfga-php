<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{UsersListUser, UsersListUserInterface};
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends IndexedCollection<UsersListUserInterface>
 *
 * @implements UsersListInterface<UsersListUserInterface>
 */
final class UsersList extends IndexedCollection implements UsersListInterface
{
    private static ?CollectionSchemaInterface $schema = null;

    protected static string $itemType = UsersListUser::class;

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
