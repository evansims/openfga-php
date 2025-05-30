<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class UsersetTreeDifference implements UsersetTreeDifferenceInterface
{
    public const string OPENAPI_MODEL = 'UsersetTree.Difference';

    private static ?SchemaInterface $schema = null;

    /**
     * @param NodeInterface $base     The base node for the difference operation
     * @param NodeInterface $subtract The node to subtract from the base
     */
    public function __construct(
        private readonly NodeInterface $base,
        private readonly NodeInterface $subtract,
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
                new SchemaProperty(name: 'base', type: 'object', className: Node::class, required: true),
                new SchemaProperty(name: 'subtract', type: 'object', className: Node::class, required: true),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getBase(): NodeInterface
    {
        return $this->base;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getSubtract(): NodeInterface
    {
        return $this->subtract;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'base' => $this->base->jsonSerialize(),
            'subtract' => $this->subtract->jsonSerialize(),
        ];
    }
}
