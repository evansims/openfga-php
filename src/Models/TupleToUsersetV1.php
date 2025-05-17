<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class TupleToUsersetV1 implements TupleToUsersetV1Interface
{
    public const OPENAPI_TYPE = 'v1.TupleToUserset';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly ObjectRelationInterface $tupleset,
        private readonly ObjectRelationInterface $computedUserset,
    ) {
    }

    public function getComputedUserset(): ObjectRelationInterface
    {
        return $this->computedUserset;
    }

    public function getTupleset(): ObjectRelationInterface
    {
        return $this->tupleset;
    }

    public function jsonSerialize(): array
    {
        return [
            'tupleset' => $this->tupleset->jsonSerialize(),
            'computed_userset' => $this->computedUserset->jsonSerialize(),
        ];
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'tupleset', type: ObjectRelation::class, required: true),
                new SchemaProperty(name: 'computed_userset', type: ObjectRelation::class, required: true),
            ],
        );
    }
}
