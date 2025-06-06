<?php

declare(strict_types=1);

namespace OpenFGA\Schemas;

use Override;

/**
 * Fluent builder for creating JSON schemas for data validation and transformation.
 *
 * This builder provides a fluent API for defining validation schemas for model classes,
 * supporting various data types, formats, and validation constraints. It's used internally
 * by the SDK to validate API responses and ensure data integrity.
 *
 * @see SchemaBuilderInterface For the complete API specification
 */
final class SchemaBuilder implements SchemaBuilderInterface
{
    /**
     * @var array<SchemaProperty>
     */
    private array $properties = [];

    /**
     * Create a new schema builder for the specified class.
     *
     * @param class-string $className The fully qualified class name to build a schema for
     */
    public function __construct(
        private readonly string $className,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function array(string $name, array $items, bool $required = false, mixed $default = null): self
    {
        $this->properties[] = new SchemaProperty(
            name: $name,
            type: 'array',
            required: $required,
            default: $default,
            items: $items,
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function boolean(string $name, bool $required = false, mixed $default = null): self
    {
        $this->properties[] = new SchemaProperty(
            name: $name,
            type: 'boolean',
            required: $required,
            default: $default,
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function date(string $name, bool $required = false, mixed $default = null): self
    {
        $this->properties[] = new SchemaProperty(
            name: $name,
            type: 'string',
            format: 'date',
            required: $required,
            default: $default,
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function datetime(string $name, bool $required = false, mixed $default = null): self
    {
        $this->properties[] = new SchemaProperty(
            name: $name,
            type: 'string',
            format: 'datetime',
            required: $required,
            default: $default,
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function integer(string $name, bool $required = false, mixed $default = null): self
    {
        $this->properties[] = new SchemaProperty(
            name: $name,
            type: 'integer',
            required: $required,
            default: $default,
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function number(string $name, bool $required = false, mixed $default = null): self
    {
        $this->properties[] = new SchemaProperty(
            name: $name,
            type: 'number',
            required: $required,
            default: $default,
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function object(string $name, string $className, bool $required = false): self
    {
        $this->properties[] = new SchemaProperty(
            name: $name,
            type: 'object',
            required: $required,
            className: $className,
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function register(): Schema
    {
        $schema = new Schema($this->className, $this->properties);
        SchemaRegistry::register($schema);

        return $schema;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function string(string $name, bool $required = false, ?string $format = null, ?array $enum = null, mixed $default = null): self
    {
        $this->properties[] = new SchemaProperty(
            name: $name,
            type: 'string',
            required: $required,
            format: $format,
            enum: $enum,
            default: $default,
        );

        return $this;
    }
}
