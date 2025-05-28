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
    /**
     * @inheritDoc
     */
    public function getBase(): UsersetInterface
    {
        return $this->base;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getSubtract(): UsersetInterface
    {
        return $this->subtract;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'base' => $this->base->jsonSerialize(),
            'subtract' => $this->subtract->jsonSerialize(),
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
                new SchemaProperty(name: 'base', type: 'object', className: Userset::class, required: true),
                new SchemaProperty(name: 'subtract', type: 'object', className: Userset::class, required: true),
            ],
        );
    }
}
