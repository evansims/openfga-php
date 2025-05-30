<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class UsersListUser implements UsersListUserInterface
{
    public const string OPENAPI_MODEL = 'UsersListUser';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string $user The user identifier for the users list user
     */
    public function __construct(
        private readonly string $user,
    ) {
    }

    /**
     * Get the string representation of this user.
     *
     * Returns the user identifier in its string format, which can be
     * a direct user ID, userset reference, or wildcard pattern.
     *
     * @return string The user identifier string
     */
    public function __toString(): string
    {
        return $this->getUser();
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    #[Override]
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): string
    {
        return $this->getUser();
    }
}
