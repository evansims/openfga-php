<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents a tuple-to-userset relationship that derives permissions from related objects.
 *
 * This enables complex authorization patterns where permissions on one object
 * are determined by relationships with other objects. For example, "users who
 * can edit a document are those who are owners of the folder containing it".
 *
 * The tupleset defines which related objects to look at, and computedUserset
 * specifies which relationship on those objects grants the permission.
 */
final class TupleToUsersetV1 implements TupleToUsersetV1Interface
{
    public const string OPENAPI_MODEL = 'v1.TupleToUserset';

    private static ?SchemaInterface $schema = null;

    /**
     * @param ObjectRelationInterface $tupleset        The tupleset object relation
     * @param ObjectRelationInterface $computedUserset The computed userset object relation
     */
    public function __construct(
        private readonly ObjectRelationInterface $tupleset,
        private readonly ObjectRelationInterface $computedUserset,
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
                new SchemaProperty(name: 'tupleset', type: 'object', className: ObjectRelation::class, required: true),
                new SchemaProperty(name: 'computedUserset', type: 'object', className: ObjectRelation::class, required: true),
            ],
        );
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
}
