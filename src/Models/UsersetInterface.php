<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface UsersetInterface extends ModelInterface
{
    public function getComputedUserset(): ?ObjectRelationInterface;

    public function getDifference(): ?DifferenceV1Interface;

    public function getDirect(): ?DirectUsersetInterface;

    public function getIntersection(): ?UsersetsInterface;

    public function getTupleToUserset(): ?TupleToUsersetV1Interface;

    public function getUnion(): ?UsersetsInterface;

    public function jsonSerialize(): array;

    public static function fromArray(array $data): self;
}
