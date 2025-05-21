<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{ConditionParametersInterface};
use OpenFGA\Models\Enums\TypeName;
use Override;

interface ConditionParameterInterface extends ModelInterface
{
    /**
     * @return ConditionParametersInterface<ConditionParameterInterface>
     */
    public function getGenericTypes(): ?ConditionParametersInterface;

    public function getTypeName(): TypeName;

    /**
     * @return array<'generic_types'|'type_name', 'TYPE_NAME_ANY'|'TYPE_NAME_BOOL'|'TYPE_NAME_DOUBLE'|'TYPE_NAME_DURATION'|'TYPE_NAME_INT'|'TYPE_NAME_IPADDRESS'|'TYPE_NAME_LIST'|'TYPE_NAME_MAP'|'TYPE_NAME_STRING'|'TYPE_NAME_TIMESTAMP'|'TYPE_NAME_UINT'|'TYPE_NAME_UNSPECIFIED'|list<array{generic_types?: array<int, mixed>, type_name: string}>>
     */
    #[Override]
    public function jsonSerialize(): array;
}
