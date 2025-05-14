<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class Userset implements UsersetInterface
{
    public const string OPENAPI_TYPE = 'Userset';

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

    public static function fromArray(array $data): self
    {
        $data = self::validatedUsersetShape($data);

        return new self(
            direct: $data['direct'] ?? null,
            computedUserset: isset($data['computed_userset']) ? ObjectRelation::fromArray($data['computed_userset']) : null,
            tupleToUserset: isset($data['tuple_to_userset']) ? TupleToUsersetV1::fromArray($data['tuple_to_userset']) : null,
            union: isset($data['union']) ? Usersets::fromArray($data['union']) : null,
            intersection: isset($data['intersection']) ? Usersets::fromArray($data['intersection']) : null,
            difference: isset($data['difference']) ? DifferenceV1::fromArray($data['difference']) : null,
        );
    }

    /**
     * Validates the shape of the array to be used as userset data. Throws an exception if the data is invalid.
     *
     * @param array{computed_userset?: ObjectRelationShape, tuple_to_userset?: TupleToUsersetShape, union?: list<UsersetShape>, intersection?: list<UsersetShape>, difference?: DifferenceShape, direct?: object} $data
     *
     * @throws InvalidArgumentException
     *
     * @return UsersetShape
     */
    public static function validatedUsersetShape(array $data): array
    {
        return $data;
    }
}
