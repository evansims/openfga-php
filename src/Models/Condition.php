<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Condition extends Model implements ConditionInterface
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
        public string $name,
        public string $expression,
        public ?ConditionParametersInterface $parameters = null,
        public ?ConditionMetadataInterface $metadata = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'expression' => $this->expression,
            'parameters' => $this->parameters?->toArray(),
            'metadata' => $this->metadata?->toArray(),
        ];
    }

    public static function fromArray(array $data): self
    {
        $name = $data['name'] ?? null;
        $expression = $data['expression'] ?? null;
        $parameters = $data['parameters'] ?? null;
        $metadata = $data['metadata'] ?? null;

        $name = $name ?: null;
        $expression = $expression ?: null;
        $parameters = $parameters ? ConditionParameters::fromArray($parameters) : null;
        $metadata = $metadata ? ConditionMetadata::fromArray($metadata) : null;

        return new self(
            name: $name,
            expression: $expression,
            parameters: $parameters,
            metadata: $metadata,
        );
    }
}
