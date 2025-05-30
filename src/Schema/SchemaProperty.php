<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

use Override;

/**
 * Represents a single property definition within a schema.
 *
 * This class defines the validation rules, type information, and metadata for
 * individual properties of OpenFGA model objects. Each property specifies how
 * a field should be validated, transformed, and mapped during object creation.
 *
 * Properties support various data types including primitives (string, int, bool),
 * complex objects, arrays, and collections, with optional validation constraints
 * such as required status, default values, format restrictions, and enumeration
 * limits.
 */
final readonly class SchemaProperty implements SchemaPropertyInterface
{
    /**
     * Create a new schema property definition.
     *
     * @param string                                             $name          The property name as it appears in the data
     * @param string                                             $type          The data type (string, integer, boolean, array, object, etc.)
     * @param bool                                               $required      Whether this property is required for validation
     * @param mixed                                              $default       Default value to use when property is missing (for optional properties)
     * @param string|null                                        $format        Additional format constraint (e.g., 'date', 'datetime')
     * @param array<string>|null                                 $enum          Array of allowed values for enumeration validation
     * @param array{type: string, className?: class-string}|null $items         Type specification for array items (when type is 'array')
     * @param class-string|null                                  $className     Fully qualified class name for object types
     * @param string|null                                        $parameterName Alternative parameter name for constructor mapping
     */
    public function __construct(
        public string $name,
        public string $type,
        public bool $required = false,
        public mixed $default = null,
        public ?string $format = null,
        public ?array $enum = null,
        public ?array $items = null,
        public ?string $className = null,
        public ?string $parameterName = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getEnum(): ?array
    {
        return $this->enum;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getItems(): ?array
    {
        return $this->items;
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
    public function getParameterName(): ?string
    {
        return $this->parameterName;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function isRequired(): bool
    {
        return $this->required;
    }
}
