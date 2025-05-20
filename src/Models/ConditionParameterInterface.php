<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{ConditionParametersInterface};
use OpenFGA\Models\Enums\TypeName;

interface ConditionParameterInterface extends ModelInterface
{
    /**
     * @return ConditionParametersInterface<ConditionParameterInterface>
     */
    public function getGenericTypes(): ?ConditionParametersInterface;

    public function getTypeName(): TypeName;

    /**
     * @return array{type_name: string, generic_types?: array<int, mixed>}
     */
    public function jsonSerialize(): array;
}
