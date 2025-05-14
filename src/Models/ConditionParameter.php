<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

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
        $response = [
            'type_name' => (string) $this->getTypeName(),
        ];

        if (null !== $this->getGenericTypes()) {
            $response['generic_types'] = $this->getGenericTypes()->jsonSerialize();
        }

        return $response;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedConditionParameterShape($data);

        return new self(
            typeName: TypeName::from($data['type_name']),
            genericTypes: isset($data['generic_types']) ? ConditionParameters::fromArray($data['generic_types']) : null,
        );
    }

    /**
     * Validates the shape of the array to be used as condition parameter data. Throws an exception if the data is invalid.
     *
     * @param array{type_name: string, generic_types?: ConditionParametersShape} $data
     *
     * @throws InvalidArgumentException
     *
     * @return ConditionParameterShape
     */
    public static function validatedConditionParameterShape(array $data): array
    {
        if (! isset($data['type_name'])) {
            throw new InvalidArgumentException('Missing required condition parameter property `type_name`');
        }

        return $data;
    }
}
