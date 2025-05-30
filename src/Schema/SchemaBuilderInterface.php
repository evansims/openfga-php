<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

/**
 * Interface for building schema definitions using the builder pattern.
 *
 * This interface provides a fluent API for constructing schema definitions that describe
 * the structure and validation rules for OpenFGA model objects. The builder pattern
 * allows for easy, readable schema creation with method chaining.
 *
 * Schema builders support all common data types including strings, integers, booleans,
 * dates, arrays, and complex objects. Each property can be configured with validation
 * rules such as required status, default values, format constraints, and enumeration
 * restrictions.
 *
 * Example usage:
 * ```php
 * $schema = $builder
 *     ->string('name', required: true)
 *     ->integer('age', required: false, default: 0)
 *     ->object('address', Address::class, required: true)
 *     ->register();
 * ```
 *
 * The built schemas are automatically registered in the SchemaRegistry for use during
 * validation and object transformation throughout the OpenFGA system.
 *
 * @see Schema The schema objects created by this builder
 * @see SchemaRegistry Where built schemas are registered
 * @see https://openfga.dev/docs/modeling OpenFGA modeling concepts for authorization systems
 */
interface SchemaBuilderInterface
{
    /**
     * Add an array property to the schema.
     *
     * @param  string                                        $name     The property name
     * @param  array{type: string, className?: class-string} $items    Array item type specification
     * @param  bool                                          $required Whether the property is required
     * @param  mixed                                         $default  Default value for optional properties
     * @return self                                          Returns the builder instance for method chaining
     */
    public function array(string $name, array $items, bool $required = false, mixed $default = null): self;

    /**
     * Add a boolean property to the schema.
     *
     * @param  string     $name     The property name
     * @param  bool       $required Whether the property is required
     * @param  mixed|null $default  Default value for optional properties
     * @return self       Returns the builder instance for method chaining
     */
    public function boolean(string $name, bool $required = false, mixed $default = null): self;

    /**
     * Add a date property to the schema.
     *
     * @param  string     $name     The property name
     * @param  bool       $required Whether the property is required
     * @param  mixed|null $default  Default value for optional properties
     * @return self       Returns the builder instance for method chaining
     */
    public function date(string $name, bool $required = false, mixed $default = null): self;

    /**
     * Add a datetime property to the schema.
     *
     * @param  string     $name     The property name
     * @param  bool       $required Whether the property is required
     * @param  mixed|null $default  Default value for optional properties
     * @return self       Returns the builder instance for method chaining
     */
    public function datetime(string $name, bool $required = false, mixed $default = null): self;

    /**
     * Add an integer property to the schema.
     *
     * @param  string     $name     The property name
     * @param  bool       $required Whether the property is required
     * @param  mixed|null $default  Default value for optional properties
     * @return self       Returns the builder instance for method chaining
     */
    public function integer(string $name, bool $required = false, mixed $default = null): self;

    /**
     * Add a number (float) property to the schema.
     *
     * @param  string     $name     The property name
     * @param  bool       $required Whether the property is required
     * @param  mixed|null $default  Default value for optional properties
     * @return self       Returns the builder instance for method chaining
     */
    public function number(string $name, bool $required = false, mixed $default = null): self;

    /**
     * Add an object property to the schema.
     *
     * @param  string       $name      The property name
     * @param  class-string $className The class name for the object property
     * @param  bool         $required  Whether the property is required
     * @return self         Returns the builder instance for method chaining
     */
    public function object(string $name, string $className, bool $required = false): self;

    /**
     * Build and register the schema.
     *
     * Creates a Schema instance with all defined properties and registers it
     * in the SchemaRegistry for use in validation.
     *
     * @return Schema The built and registered schema
     */
    public function register(): Schema;

    /**
     * Add a string property to the schema.
     *
     * @param  string             $name     The property name
     * @param  bool               $required Whether the property is required
     * @param  string|null        $format   String format constraint (e.g., 'date', 'datetime')
     * @param  array<string>|null $enum     Array of allowed string values
     * @param  mixed              $default  Default value for optional properties
     * @return self               Returns the builder instance for method chaining
     */
    public function string(string $name, bool $required = false, ?string $format = null, ?array $enum = null, mixed $default = null): self;
}
