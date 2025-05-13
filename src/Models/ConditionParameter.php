<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class ConditionParameter implements ConditionParameterInterface
{
    public function __construct(
        private TypeName $typeName,
        private ?ConditionParametersInterface $genericTypes = null,
    ) {
    }

    public function getGenericTypes(): ?ConditionParametersInterface
    {
        return $this->genericTypes;
    }

    public function getTypeName(): TypeName
    {
        return $this->typeName;
    }

    public function jsonSerialize(): array
    {
        return [
            'type_name' => (string) $this->typeName,
            'generic_types' => $this->genericTypes?->jsonSerialize(),
        ];
    }

    public static function fromArray(array $data): self
    {
        $typeName = $data['type_name'] ?? null;
        $genericTypes = $data['generic_types'] ?? null;

        $typeName = $typeName ? TypeName::from($typeName) : null;
        $genericTypes = $genericTypes ? ConditionParameters::fromArray($genericTypes) : null;

        return new self(
            typeName: $typeName,
            genericTypes: $genericTypes,
        );
    }
}
