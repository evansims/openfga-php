<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class ConditionParameter extends Model implements ConditionParameterInterface
{
    public function __construct(
        public TypeName $typeName,
        public ?ConditionParametersInterface $genericTypes = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'type_name' => $this->typeName->value,
            'generic_types' => $this->genericTypes?->toArray(),
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
