<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents a filter for limiting users by type and optional relation.
 *
 * UserTypeFilter allows you to constrain authorization queries to specific
 * user types, optionally including only users that have a particular relation.
 * This is useful for filtering results when listing users or performing
 * authorization checks on specific user categories.
 *
 * Use this when you need to limit authorization operations to specific
 * types of users in your system.
 */
final class UserTypeFilter implements UserTypeFilterInterface
{
    public const string OPENAPI_MODEL = 'UserTypeFilter';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string      $type     The type name for the user type filter
     * @param string|null $relation Optional relation name for the filter
     */
    public function __construct(
        private readonly string $type,
        private readonly ?string $relation = null,
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
                new SchemaProperty(name: 'relation', type: 'string', required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRelation(): ?string
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
        return array_filter([
            'type' => $this->type,
            'relation' => $this->relation,
        ], static fn ($value): bool => null !== $value);
    }
}
