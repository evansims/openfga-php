<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface ConditionParameterInterface extends ModelInterface
{
    public function getGenericTypes(): ?ConditionParametersInterface;

    public function getTypeName(): TypeName;
}
