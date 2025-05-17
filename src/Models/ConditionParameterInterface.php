<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type ConditionParameterShape = array{type_name: string, generic_types?: ConditionParametersShape}
 */
interface ConditionParameterInterface extends ModelInterface
{
    public function getGenericTypes(): ?ConditionParametersInterface;

    public function getTypeName(): TypeName;

    /**
     * @return ConditionParameterShape
     */
    public function jsonSerialize(): array;
}
