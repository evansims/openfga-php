<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents a reference to a specific relation with optional conditions.
 *
 * A RelationReference identifies a relation within your authorization model,
 * optionally with an associated condition that must be satisfied. This enables
 * conditional relationships where the relation is only valid when certain
 * runtime conditions are met.
 *
 * Use this when defining relation constraints or implementing attribute-based
 * access control patterns in your authorization model.
 */
final class RelationReference implements RelationReferenceInterface
{
    public const string OPENAPI_MODEL = 'RelationReference';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string      $type      The type name for the relation reference
     * @param string|null $relation  Optional relation name
     * @param object|null $wildcard  Optional wildcard object
     * @param string|null $condition Optional condition name
     */
    public function __construct(
        private readonly string $type,
        private readonly ?string $relation = null,
        private readonly ?object $wildcard = null,
        private readonly ?string $condition = null,
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
                new SchemaProperty(name: 'type', type: 'string', required: true),
                new SchemaProperty(name: 'relation', type: 'string', required: false),
                new SchemaProperty(name: 'wildcard', type: 'object', required: false),
                new SchemaProperty(name: 'condition', type: 'string', required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getCondition(): ?string
    {
        return $this->condition;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRelation(): ?string
    {
        return $this->relation;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getWildcard(): ?object
    {
        return $this->wildcard;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        $result = ['type' => $this->type];

        if (null !== $this->relation && '' !== $this->relation) {
            $result['relation'] = $this->relation;
        }

        if (null !== $this->wildcard) {
            $result['wildcard'] = $this->wildcard;
        }

        if (null !== $this->condition && '' !== $this->condition) {
            $result['condition'] = $this->condition;
        }

        return $result;
    }
}
