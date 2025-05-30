<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class Computed implements ComputedInterface
{
    public const string OPENAPI_MODEL = 'Computed';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string $userset The userset specification for the computed relation
     */
    public function __construct(
        private readonly string $userset,
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
                new SchemaProperty(name: 'userset', type: 'string', required: true),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getUserset(): string
    {
        return $this->userset;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'userset' => $this->userset,
        ];
    }
}
