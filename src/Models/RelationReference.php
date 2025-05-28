<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class RelationReference implements RelationReferenceInterface
{
    public const OPENAPI_MODEL = 'RelationReference';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $type,
        private readonly ?string $relation = null,
        private readonly ?object $wildcard = null,
        private readonly ?string $condition = null,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getCondition(): ?string
    {
        return $this->condition;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getRelation(): ?string
    {
        return $this->relation;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getWildcard(): ?object
    {
        return $this->wildcard;
    }

    #[Override]
    /**
     * @inheritDoc
     */
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

    #[Override]
    /**
     * @inheritDoc
     */
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
}
