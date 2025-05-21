<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class UsersListUser implements UsersListUserInterface
{
    /**
     * TODO: This approach won't work. We'll most likely need to turn this into some sort of custom serializer.
     * We'll need to replace UsersListUser and UsersList. UsersListUser shouldn't need to exist.
     */
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $user,
    ) {
    }

    public function __toString(): string
    {
        return $this->getUser();
    }

    #[Override]
    public function getUser(): string
    {
        return $this->user;
    }

    #[Override]
    public function jsonSerialize(): string
    {
        return $this->getUser();
    }

    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'user', type: 'string', required: true),
            ],
        );
    }
}
