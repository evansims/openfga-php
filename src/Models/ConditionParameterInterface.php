<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface ConditionParameterInterface extends ModelInterface
{
    public function getGenericTypes(): ?ConditionParametersInterface;

    public function getTypeName(): TypeName;

    /**
     * @return array{type_name: string, generic_types?: array{module: string, source_info: array{file: string}}}
     */
    public function jsonSerialize(): array;

    /**
     * @param array{type_name: string, generic_types?: array{module: string, source_info: array{file: string}}} $data
     */
    public static function fromArray(array $data): static;
}
