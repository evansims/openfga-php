<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class Computed implements ComputedInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $userset,
    ) {
    }

    public function getUserset(): string
    {
        return $this->userset;
    }

    public function jsonSerialize(): array
    {
        return [
            'userset' => $this->userset,
        ];
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'userset', type: 'string', required: true),
            ],
        );
    }
}
