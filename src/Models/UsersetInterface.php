<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface UsersetInterface extends ModelInterface
{
    public function getComputedUserset(): ?ObjectRelation;

    public function getDifference(): ?DifferenceV1;

    public function getDirect(): ?DirectUserset;

    public function getIntersection(): ?Usersets;

    public function getTupleToUserset(): ?TupleToUsersetV1;

    public function getUnion(): ?Usersets;
}
