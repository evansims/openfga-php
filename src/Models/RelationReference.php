<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class RelationReference implements RelationReferenceInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $type,
        private readonly ?string $relation = null,
        private readonly ?object $wildcard = null,
        private readonly ?string $condition = null,
    ) {
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getWildcard(): ?object
    {
        return $this->wildcard;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter(
            [
                'type' => $this->type,
                'relation' => $this->relation,
                'wildcard' => $this->wildcard,
                'condition' => $this->condition,
            ],
            static fn ($v) => null !== $v,
        );
    }

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
