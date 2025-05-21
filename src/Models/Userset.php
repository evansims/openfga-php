<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{Usersets, UsersetsInterface};

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class Userset implements UsersetInterface
{
    public const OPENAPI_TYPE = 'Userset';

    private static ?SchemaInterface $schema = null;

    /**
     * @param null|object                              $direct
     * @param null|ObjectRelationInterface             $computedUserset
     * @param null|TupleToUsersetV1Interface           $tupleToUserset
     * @param null|UsersetsInterface<UsersetInterface> $union
     * @param null|UsersetsInterface<UsersetInterface> $intersection
     * @param null|DifferenceV1Interface               $difference
     */
    public function __construct(
        private readonly ?object $direct = null,
        private readonly ?ObjectRelationInterface $computedUserset = null,
        private readonly ?TupleToUsersetV1Interface $tupleToUserset = null,
        private readonly ?UsersetsInterface $union = null,
        private readonly ?UsersetsInterface $intersection = null,
        private readonly ?DifferenceV1Interface $difference = null,
    ) {
    }

    #[Override]
    public function getComputedUserset(): ?ObjectRelationInterface
    {
        return $this->computedUserset;
    }

    #[Override]
    public function getDifference(): ?DifferenceV1Interface
    {
        return $this->difference;
    }

    #[Override]
    public function getDirect(): ?object
    {
        return $this->direct;
    }

    #[Override]
    public function getIntersection(): ?UsersetsInterface
    {
        return $this->intersection;
    }

    #[Override]
    public function getTupleToUserset(): ?TupleToUsersetV1Interface
    {
        return $this->tupleToUserset;
    }

    #[Override]
    public function getUnion(): ?UsersetsInterface
    {
        return $this->union;
    }

    #[Override]
    public function jsonSerialize(): array
    {
        return array_filter([
            'direct' => $this->direct,
            'computed_userset' => $this->computedUserset?->jsonSerialize(),
            'tuple_to_userset' => $this->tupleToUserset?->jsonSerialize(),
            'union' => $this->union?->jsonSerialize(),
            'intersection' => $this->intersection?->jsonSerialize(),
            'difference' => $this->difference?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);
    }

    #[Override]
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
