<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents a specific user object with type and identifier.
 *
 * A UserObject provides a structured way to represent users in your authorization
 * system with both a type (like "user," "service," "bot") and a unique identifier.
 * This allows for clear categorization of different kinds of entities that can
 * have permissions in your system.
 *
 * Use this when you need to represent users in a structured format rather than
 * simple string identifiers.
 */
final class UserObject implements UserObjectInterface
{
    public const string OPENAPI_MODEL = 'UserObject';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string $type The type of the user object
     * @param string $id   The unique identifier of the user object
     */
    public function __construct(
        private readonly string $type,
        private readonly string $id,
    ) {
    }

    /**
     * Get the string representation of this user object.
     *
     * Returns the user object in the standard "type:id" format commonly
     * used throughout OpenFGA for representing user identifiers.
     *
     * @return string The user object formatted as "type:id"
     */
    public function __toString(): string
    {
        return $this->type . ':' . $this->id;
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
                new SchemaProperty(name: 'type', type: 'string', required: true),
                new SchemaProperty(name: 'id', type: 'string', required: true),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'id' => $this->id,
        ];
    }
}
