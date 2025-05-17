<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class Userset implements UsersetInterface
{
    public const OPENAPI_TYPE = 'Userset';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private ?object $direct = null,
        private ?ObjectRelationInterface $computedUserset = null,
        private ?TupleToUsersetV1Interface $tupleToUserset = null,
        private ?UsersetsInterface $union = null,
        private ?UsersetsInterface $intersection = null,
        private ?DifferenceV1Interface $difference = null,
    ) {
    }

    public function getComputedUserset(): ?ObjectRelationInterface
    {
        return $this->computedUserset;
    }

    public function getDifference(): ?DifferenceV1Interface
    {
        return $this->difference;
    }

    public function getDirect(): ?object
    {
        return $this->direct;
    }

    public function getIntersection(): ?UsersetsInterface
    {
        return $this->intersection;
    }

    public function getTupleToUserset(): ?TupleToUsersetV1Interface
    {
        return $this->tupleToUserset;
    }

    public function getUnion(): ?UsersetsInterface
    {
        return $this->union;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'direct' => $this->direct,
            'computed_userset' => $this->computedUserset?->jsonSerialize(),
            'tuple_to_userset' => $this->tupleToUserset?->jsonSerialize(),
            'union' => $this->union?->jsonSerialize(),
            'intersection' => $this->intersection?->jsonSerialize(),
            'difference' => $this->difference?->jsonSerialize(),
        ], static fn ($value) => null !== $value);
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'direct', type: 'object', required: false),
                new SchemaProperty(name: 'computed_userset', type: ObjectRelation::class, required: false),
                new SchemaProperty(name: 'tuple_to_userset', type: TupleToUsersetV1::class, required: false),
                new SchemaProperty(name: 'union', type: Usersets::class, required: false),
                new SchemaProperty(name: 'intersection', type: Usersets::class, required: false),
                new SchemaProperty(name: 'difference', type: DifferenceV1::class, required: false),
            ],
        );
    }
}
