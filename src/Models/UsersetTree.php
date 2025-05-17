<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class UsersetTree implements UsersetTreeInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private NodeInterface $root,
    ) {
    }

    public function getRoot(): NodeInterface
    {
        return $this->root;
    }

    public function jsonSerialize(): array
    {
        return [
            'root' => $this->getRoot()->jsonSerialize(),
        ];
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'root', type: Node::class, required: true),
            ],
        );
    }
}
