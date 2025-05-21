<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class Computed implements ComputedInterface
{
    public const OPENAPI_MODEL = 'Computed';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $userset,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getUserset(): string
    {
        return $this->userset;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'userset' => $this->userset,
        ];
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
                new SchemaProperty(name: 'userset', type: 'string', required: true),
            ],
        );
    }
}
