<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

final class SchemaBuilder
{
    /**
     * @var array<SchemaProperty>
     */
    private array $properties = [];

    /**
     * @param class-string $className
     */
    public function __construct(
        private readonly string $className,
    ) {
    }

    /**
     * Add an array property.
     *
     * @param array{type: string, className?: class-string} $items
     * @param string                                        $name
     * @param bool                                          $required
     * @param mixed                                         $default
     */
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
     * Add a boolean property.
     *
     * @param string     $name
     * @param bool       $required
     * @param null|mixed $default
     */
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
     * Add a date property.
     *
     * @param string     $name
     * @param bool       $required
     * @param null|mixed $default
     */
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
     * Add a datetime property.
     *
     * @param string     $name
     * @param bool       $required
     * @param null|mixed $default
     */
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
     * Add an integer property.
     *
     * @param string     $name
     * @param bool       $required
     * @param null|mixed $default
     */
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
     * Add a number (float) property.
     *
     * @param string     $name
     * @param bool       $required
     * @param null|mixed $default
     */
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
     * Add an object property.
     *
     * @param class-string $className
     * @param string       $name
     * @param bool         $required
     */
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
     * Build and register the schema.
     */
    public function register(): Schema
    {
        $schema = new Schema($this->className, $this->properties);
        SchemaRegistry::register($schema);

        return $schema;
    }

    /**
     * Add a string property.
     *
     * @param string             $name
     * @param bool               $required
     * @param null|string        $format
     * @param null|array<string> $enum
     * @param mixed              $default
     */
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
