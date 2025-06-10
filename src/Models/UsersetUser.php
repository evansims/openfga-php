<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents a user reference through a userset relationship.
 *
 * UsersetUser defines a user specification in the format "object#relation"
 * where users are identified through their relationship to an object rather
 * than direct user identifiers. This enables dynamic user groups based on
 * relationships.
 *
 * Use this when you need to reference users through relationship-based
 * groups rather than explicit user lists.
 */
final class UsersetUser implements UsersetUserInterface
{
    public const string OPENAPI_MODEL = 'UsersetUser';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string $type     The type of the userset user
     * @param string $id       The unique identifier of the userset user
     * @param string $relation The relation for the userset user
     */
    public function __construct(
        private readonly string $type,
        private readonly string $id,
        private readonly string $relation,
    ) {
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
                new SchemaProperty(name: 'relation', type: 'string', required: true),
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
    public function getRelation(): string
    {
        return $this->relation;
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
            'relation' => $this->relation,
        ];
    }
}
