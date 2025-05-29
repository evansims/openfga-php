<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class UsersetTree implements UsersetTreeInterface
{
    public const OPENAPI_TYPE = 'UsersetTree';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly NodeInterface $root,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getRoot(): NodeInterface
    {
        return $this->root;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'root' => $this->root->jsonSerialize(),
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
                new SchemaProperty(name: 'root', type: 'object', className: Node::class, required: true),
            ],
        );
    }
}
