<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface TupleKeyInterface extends ModelInterface
{
    public function getCondition(): ?ConditionInterface;

    public function getObject(): ?string;

    public function getRelation(): ?string;

    public function getUser(): ?string;
}
