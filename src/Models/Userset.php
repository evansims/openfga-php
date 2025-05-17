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

    public function jsonSerialize(): array
    {
        $response = [];

        if (null !== $this->getDirect()) {
            $response['direct'] = $this->getDirect();
        }

        if (null !== $this->getComputedUserset()) {
            $response['computed_userset'] = $this->getComputedUserset()->jsonSerialize();
        }

        if (null !== $this->getTupleToUserset()) {
            $response['tuple_to_userset'] = $this->getTupleToUserset()->jsonSerialize();
        }

        if (null !== $this->getUnion()) {
            $response['union'] = $this->getUnion()->jsonSerialize();
        }

        if (null !== $this->getIntersection()) {
            $response['intersection'] = $this->getIntersection()->jsonSerialize();
        }

        if (null !== $this->getDifference()) {
            $response['difference'] = $this->getDifference()->jsonSerialize();
        }

        return $response;
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
