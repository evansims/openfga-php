<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class UserTypeFilter implements UserTypeFilterInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $type,
        private readonly ?string $relation = null,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getRelation(): ?string
    {
        return $this->relation;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'type' => $this->type,
            'relation' => $this->relation,
        ], static fn ($value): bool => null !== $value);
    }

    #[Override]
    /**
     * @inheritDoc
     */
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
}
