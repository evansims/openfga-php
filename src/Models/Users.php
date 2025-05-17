<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};

final class Users implements UsersInterface
{
    use IndexedCollectionTrait;

    private static ?CollectionSchemaInterface $schema = null;

    /**
     * @param iterable<UserInterface>|UserInterface ...$users
     */
    public function __construct(iterable | UserInterface ...$users)
    {
        foreach ($users as $user) {
            $this->add($user);
        }
    }

    public function add(UserInterface $user): void
    {
        $this->models[] = $user;
    }

    public function current(): UserInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?UserInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function schema(): CollectionSchemaInterface
    {
        return self::$schema ??= new CollectionSchema(
            className: self::class,
            itemType: User::class,
            requireItems: false,
        );
    }
}
