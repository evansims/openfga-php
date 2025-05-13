<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Userset implements UsersetInterface
{
    use ModelTrait;

    public function __construct(
        private ?DirectUsersetInterface $direct = null,
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

    public function getDirect(): ?DirectUsersetInterface
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

        if ($this->direct !== null) {
            $response['direct'] = $this->direct->jsonSerialize();
        }

        if ($this->computedUserset !== null) {
            $response['computed_userset'] = $this->computedUserset->jsonSerialize();
        }

        if ($this->tupleToUserset !== null) {
            $response['tuple_to_userset'] = $this->tupleToUserset->jsonSerialize();
        }

        if ($this->union !== null) {
            $response['union'] = $this->union->jsonSerialize();
        }

        if ($this->intersection !== null) {
            $response['intersection'] = $this->intersection->jsonSerialize();
        }

        if ($this->difference !== null) {
            $response['difference'] = $this->difference->jsonSerialize();
        }

        return $response;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            direct: isset($data['direct']) ? DirectUserset::fromArray($data['direct']) : null,
            computedUserset: isset($data['computed_userset']) ? ObjectRelation::fromArray($data['computed_userset']) : null,
            tupleToUserset: isset($data['tuple_to_userset']) ? TupleToUsersetV1::fromArray($data['tuple_to_userset']) : null,
            union: isset($data['union']) ? Usersets::fromArray($data['union']) : null,
            intersection: isset($data['intersection']) ? Usersets::fromArray($data['intersection']) : null,
            difference: isset($data['difference']) ? DifferenceV1::fromArray($data['difference']) : null,
        );
    }
}
