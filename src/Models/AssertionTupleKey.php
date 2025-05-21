<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class AssertionTupleKey implements AssertionTupleKeyInterface
{
    public const OPENAPI_MODEL = 'AssertionTupleKey';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $user,
        private readonly string $relation,
        private readonly string $object,
    ) {
    }

    #[Override]
    public function getObject(): string
    {
        return $this->object;
    }

    #[Override]
    public function getRelation(): string
    {
        return $this->relation;
    }

    #[Override]
    public function getUser(): string
    {
        return $this->user;
    }

    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'user' => $this->user,
            'relation' => $this->relation,
            'object' => $this->object,
        ];
    }

    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'user', type: 'string', required: true),
                new SchemaProperty(name: 'relation', type: 'string', required: true),
                new SchemaProperty(name: 'object', type: 'string', required: true),
            ],
        );
    }
}
