<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class UsersetTreeDifference implements UsersetTreeDifferenceInterface
{
    public const OPENAPI_MODEL = 'UsersetTree.Difference';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly NodeInterface $base,
        private readonly NodeInterface $subtract,
    ) {
    }

    public function getBase(): NodeInterface
    {
        return $this->base;
    }

    public function getSubtract(): NodeInterface
    {
        return $this->subtract;
    }

    public function jsonSerialize(): array
    {
        return [
            'base' => $this->base->jsonSerialize(),
            'subtract' => $this->subtract->jsonSerialize(),
        ];
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'base', type: Node::class, required: true),
                new SchemaProperty(name: 'subtract', type: Node::class, required: true),
            ],
        );
    }
}
