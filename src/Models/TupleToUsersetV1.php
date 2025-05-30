<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class TupleToUsersetV1 implements TupleToUsersetV1Interface
{
    public const OPENAPI_TYPE = 'v1.TupleToUserset';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly ObjectRelationInterface $tupleset,
        private readonly ObjectRelationInterface $computedUserset,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getComputedUserset(): ObjectRelationInterface
    {
        return $this->computedUserset;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTupleset(): ObjectRelationInterface
    {
        return $this->tupleset;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'tupleset' => $this->tupleset->jsonSerialize(),
            'computedUserset' => $this->computedUserset->jsonSerialize(),
        ];
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
                new SchemaProperty(name: 'tupleset', type: 'object', className: ObjectRelation::class, required: true),
                new SchemaProperty(name: 'computedUserset', type: 'object', className: ObjectRelation::class, required: true),
            ],
        );
    }
}
