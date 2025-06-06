<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents the evaluation tree for determining user access.
 *
 * When OpenFGA evaluates whether a user has access to an object, it builds
 * a tree structure showing all the authorization paths that were considered.
 * The UsersetTree contains this evaluation tree with a root node that
 * represents the starting point of the access evaluation.
 *
 * This is primarily used for debugging authorization decisions and understanding
 * why access was granted or denied in complex permission scenarios.
 */
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
