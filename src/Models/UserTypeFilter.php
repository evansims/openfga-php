<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class UserTypeFilter implements UserTypeFilterInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private string $type,
        private ?string $relation = null,
    ) {
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'type' => $this->type,
            'relation' => $this->relation,
        ], static fn ($value) => null !== $value);
    }

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
