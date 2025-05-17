<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class AssertionTupleKey implements AssertionTupleKeyInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $user,
        private readonly string $relation,
        private readonly string $object,
    ) {
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function jsonSerialize(): array
    {
        return [
            'user' => $this->getUser(),
            'relation' => $this->getRelation(),
            'object' => $this->getObject(),
        ];
    }

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
