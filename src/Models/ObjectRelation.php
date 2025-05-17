<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class ObjectRelation implements ObjectRelationInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private ?string $object = null,
        private ?string $relation = null,
    ) {
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function jsonSerialize(): array
    {
        $response = [];

        if (null !== $this->getObject()) {
            $response['object'] = $this->getObject();
        }

        if (null !== $this->getRelation()) {
            $response['relation'] = $this->getRelation();
        }

        return $response;
    }

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
}
