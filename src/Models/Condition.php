<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class Condition implements ConditionInterface
{
    /**
     * Construct a Condition object.
     *
     * @param string                            $name       A unique name for the condition.
     * @param string                            $expression A Google CEL expression, expressed as a string.
     * @param null|ConditionParametersInterface $parameters A collection of parameter names to the parameter's defined type reference.
     * @param null|ConditionMetadataInterface   $metadata   The collection of metadata that should be associated with the condition.
     */
    public function __construct(
        private string $name,
        private string $expression,
        private ?ConditionParametersInterface $parameters = null,
        private ?ConditionMetadataInterface $metadata = null,
    ) {
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function getMetadata(): ?ConditionMetadataInterface
    {
        return $this->metadata;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParameters(): ?ConditionParametersInterface
    {
        return $this->parameters;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'expression' => $this->expression,
            'parameters' => $this->parameters?->jsonSerialize(),
            'metadata' => $this->metadata?->jsonSerialize(),
        ];
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedConditionShape($data);

        return new self(
            name: $data['name'],
            expression: $data['expression'],
            parameters: isset($data['parameters']) ? ConditionParameters::fromArray($data['parameters']) : null,
            metadata: isset($data['metadata']) ? ConditionMetadata::fromArray($data['metadata']) : null,
        );
    }

    /**
     * Validates the shape of the array to be used as condition data. Throws an exception if the data is invalid.
     *
     * @param array{name: string, expression: string, parameters?: ConditionParametersShape, metadata?: ConditionMetadataShape} $data
     *
     * @throws InvalidArgumentException
     *
     * @return ConditionShape
     */
    public static function validatedConditionShape(array $data): array
    {
        if (! isset($data['name'])) {
            throw new InvalidArgumentException('Missing required condition property `name`');
        }

        if (! isset($data['expression'])) {
            throw new InvalidArgumentException('Missing required condition property `expression`');
        }

        return $data;
    }
}
