<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class DifferenceV1 implements DifferenceV1Interface
{
    public const OPENAPI_MODEL = 'v1.Difference';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly UsersetInterface $base,
        private readonly UsersetInterface $subtract,
    ) {
    }

    #[Override]
    public function getBase(): UsersetInterface
    {
        return $this->base;
    }

    #[Override]
    public function getSubtract(): UsersetInterface
    {
        return $this->subtract;
    }

    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'base' => $this->base->jsonSerialize(),
            'subtract' => $this->subtract->jsonSerialize(),
        ];
    }

    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'base', type: Userset::class, required: true),
                new SchemaProperty(name: 'subtract', type: Userset::class, required: true),
            ],
        );
    }
}
