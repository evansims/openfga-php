<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents a user entry in a users list response.
 *
 * UsersListUser provides a simple wrapper around user identifiers returned
 * from list operations. It ensures consistent representation of users in
 * lists while providing convenient access to the user identifier string.
 *
 * Use this when working with user lists returned from OpenFGA queries
 * or when you need a structured representation of user identifiers.
 */
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
