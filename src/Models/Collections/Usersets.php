<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{Userset, UsersetInterface};
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};
use Override;

/**
 * @extends IndexedCollection<UsersetInterface>
 *
 * @implements UsersetsInterface<UsersetInterface>
 */
final class Usersets extends IndexedCollection implements UsersetsInterface
{
    private static ?CollectionSchemaInterface $schema = null;

    protected static string $itemType = Userset::class;

    /**
     * @return array{child: array<mixed>}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        // For union/intersection, we need to return as {child: [...]}
        return [
            'child' => parent::jsonSerialize(),
        ];
    }

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
            wrapperKey: 'child',
        );
    }
}
