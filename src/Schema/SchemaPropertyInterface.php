<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

/**
 * Interface for schema property definitions.
 *
 * This interface defines the contract for schema property objects that specify
 * validation rules, type information, and metadata for individual properties
 * of OpenFGA model objects. Each property defines how a field should be
 * validated, transformed, and mapped during object creation.
 *
 * Properties support various data types including primitives (string, int, bool),
 * complex objects, arrays, and collections, with optional validation constraints
 * such as required status, default values, format restrictions, and enumeration
 * limits.
 *
 * @see SchemaProperty Concrete implementation of schema property definitions
 */
interface SchemaPropertyInterface
{
    /**
     * Get the fully qualified class name for object types.
     *
     * @return class-string|null Class name for object types or null if not an object type
     */
    public function getClassName(): ?string;

    /**
     * Get the default value to use when property is missing.
     *
     * @return mixed Default value for optional properties
     */
    public function getDefault(): mixed;

    /**
     * Get the array of allowed values for enumeration validation.
     *
     * @return array<string>|null Array of allowed values or null if not an enumeration
     */
    public function getEnum(): ?array;

    /**
     * Get the additional format constraint for this property.
     *
     * @return string|null Format constraint (e.g., 'date', 'datetime') or null if none
     */
    public function getFormat(): ?string;

    /**
     * Get the type specification for array items.
     *
     * @return array{type: string, className?: class-string}|null Type specification for array items or null
     */
    public function getItems(): ?array;

    /**
     * Get the property name as it appears in the data.
     *
     * @return string The property name
     */
    public function getName(): string;

    /**
     * Get the alternative parameter name for constructor mapping.
     *
     * @return string|null Alternative parameter name or null if using default mapping
     */
    public function getParameterName(): ?string;

    /**
     * Get the data type for this property.
     *
     * @return string The data type (string, integer, boolean, array, object, etc.)
     */
    public function getType(): string;

    /**
     * Check if this property is required for validation.
     *
     * @return bool True if the property is required, false otherwise
     */
    public function isRequired(): bool;
}
