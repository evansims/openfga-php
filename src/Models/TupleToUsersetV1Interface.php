<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface TupleToUsersetV1Interface extends ModelInterface
{
    public function getComputedUserset(): ObjectRelation;

    public function getTupleset(): ObjectRelation;
}
