<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{ConditionParameters, ConditionParametersInterface};

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class Condition implements ConditionInterface
{
    public const OPENAPI_MODEL = 'Condition';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string                                                         $name       A unique name for the condition.
     * @param string                                                         $expression A Google CEL expression, expressed as a string.
     * @param null|ConditionParametersInterface<ConditionParameterInterface> $parameters A collection of parameter names to the parameter's defined type reference.
     * @param null|ConditionMetadataInterface                                $metadata   The collection of metadata that should be associated with the condition.
     */
    public function __construct(
        private readonly string $name,
        private readonly string $expression,
        private readonly ?ConditionParametersInterface $parameters = null,
        private readonly ?ConditionMetadataInterface $metadata = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getMetadata(): ?ConditionMetadataInterface
    {
        return $this->metadata;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getParameters(): ?ConditionParametersInterface
    {
        return $this->parameters;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return array_filter([
            'name' => $this->name,
            'expression' => $this->expression,
            'parameters' => $this->parameters?->jsonSerialize(),
            'metadata' => $this->metadata?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'name', type: 'string', required: true),
                new SchemaProperty(name: 'expression', type: 'string', required: true),
                new SchemaProperty(name: 'parameters', type: 'object', className: ConditionParameters::class, required: false),
                new SchemaProperty(name: 'metadata', type: 'object', className: ConditionMetadata::class, required: false),
            ],
        );
    }
}
