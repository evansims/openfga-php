<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class UsersetTreeDifference implements UsersetTreeDifferenceInterface
{
    public const OPENAPI_MODEL = 'UsersetTree.Difference';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly NodeInterface $base,
        private readonly NodeInterface $subtract,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getBase(): NodeInterface
    {
        return $this->base;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getSubtract(): NodeInterface
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
                new SchemaProperty(name: 'base', type: Node::class, required: true),
                new SchemaProperty(name: 'subtract', type: Node::class, required: true),
            ],
        );
    }
}
