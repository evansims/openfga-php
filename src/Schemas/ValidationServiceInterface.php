<?php

declare(strict_types=1);

namespace OpenFGA\Schemas;

use OpenFGA\Exceptions\SerializationException;

/**
 * Service for validating data against schemas.
 *
 * This service encapsulates the validation logic, separating it from
 * object construction concerns. It validates data structures against
 * registered schemas and provides detailed error reporting.
 */
interface ValidationServiceInterface
{
    /**
     * Check if a schema is registered for a class.
     *
     * @param  string $className The class name to check
     * @return bool   True if schema is registered
     */
    public function hasSchema(string $className): bool;

    /**
     * Register a schema for validation.
     *
     * @param  SchemaInterface $schema The schema to register
     * @return self            For method chaining
     */
    public function registerSchema(SchemaInterface $schema): self;

    /**
     * Validate data against a schema.
     *
     * Validates the provided data against the schema for the specified class.
     * This method only validates structure and types, it does not construct objects.
     *
     * @param mixed  $data      The data to validate
     * @param string $className The class name whose schema to validate against
     *
     * @throws SerializationException If validation fails
     *
     * @return array<string, mixed> The validated data (may be normalized/cleaned)
     */
    public function validate(mixed $data, string $className): array;

    /**
     * Validate a property value against its schema definition.
     *
     * @param mixed                   $value    The value to validate
     * @param SchemaPropertyInterface $property The property schema
     * @param string                  $path     The property path for error reporting
     *
     * @throws SerializationException If validation fails
     *
     * @return mixed The validated value
     */
    public function validateProperty(mixed $value, SchemaPropertyInterface $property, string $path): mixed;
}
