<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{ConditionParameters, ConditionParametersInterface};
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents an ABAC (Attribute-Based Access Control) condition in your authorization model.
 *
 * A Condition defines a logical expression that must evaluate to true for
 * authorization to be granted. It includes the expression code, parameter
 * definitions, and optional metadata. Conditions enable context-aware
 * authorization decisions based on attributes of users, resources, and environment.
 *
 * Use this when implementing fine-grained access control that depends on
 * runtime attributes and contextual information.
 */
final class Condition implements ConditionInterface
{
    public const string OPENAPI_MODEL = 'Condition';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string                            $name       A unique name for the condition
     * @param string                            $expression A Google CEL expression, expressed as a string
     * @param ConditionParametersInterface|null $parameters A collection of parameter names to the parameter's defined type reference
     * @param ConditionMetadataInterface|null   $metadata   The collection of metadata that should be associated with the condition
     * @param array<string, mixed>|null         $context    The context for the condition
     */
    public function __construct(
        private readonly string $name,
        private readonly string $expression,
        private readonly ?ConditionParametersInterface $parameters = null,
        private readonly ?ConditionMetadataInterface $metadata = null,
        private readonly ?array $context = null,
    ) {
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
                new SchemaProperty(name: 'context', type: 'object', required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getContext(): ?array
    {
        return $this->context;
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
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return array_filter([
            'name' => $this->name,
            'expression' => $this->expression,
            'parameters' => $this->parameters?->jsonSerialize(),
            'metadata' => $this->metadata?->jsonSerialize(),
            'context' => $this->context,
        ], static fn ($value): bool => null !== $value);
    }
}
