<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class ObjectRelation implements ObjectRelationInterface
{
    public const string OPENAPI_MODEL = 'ObjectRelation';

    private static ?SchemaInterface $schema = null;

    /**
     * @param ?string $object   The object identifier or null
     * @param ?string $relation The relation name or null
     */
    public function __construct(
        private readonly ?string $object = null,
        private readonly ?string $relation = null,
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
                new SchemaProperty(name: 'object', type: 'string', required: false),
                new SchemaProperty(name: 'relation', type: 'string', required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getObject(): ?string
    {
        return $this->object;
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
    public function jsonSerialize(): array
    {
        return array_filter([
            'object' => $this->object,
            'relation' => $this->relation,
        ], static fn ($value): bool => null !== $value && '' !== $value);
    }
}
