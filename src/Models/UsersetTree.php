<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class UsersetTree implements UsersetTreeInterface
{
    public const string OPENAPI_MODEL = 'UsersetTree';

    private static ?SchemaInterface $schema = null;

    /**
     * @param NodeInterface $root The root node of the userset tree
     */
    public function __construct(
        private readonly NodeInterface $root,
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
                new SchemaProperty(name: 'root', type: 'object', className: Node::class, required: true),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRoot(): NodeInterface
    {
        return $this->root;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'root' => $this->root->jsonSerialize(),
        ];
    }
}
