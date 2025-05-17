<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class DifferenceV1 implements DifferenceV1Interface
{
    public const OPENAPI_TYPE = 'v1.Difference';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private UsersetInterface $base,
        private UsersetInterface $subtract,
    ) {
    }

    public function getBase(): UsersetInterface
    {
        return $this->base;
    }

    public function getSubtract(): UsersetInterface
    {
        return $this->subtract;
    }

    public function jsonSerialize(): array
    {
        return [
            'base' => $this->getBase()->jsonSerialize(),
            'subtract' => $this->getSubtract()->jsonSerialize(),
        ];
    }

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
