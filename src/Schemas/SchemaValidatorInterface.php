<?php

declare(strict_types=1);

namespace OpenFGA\Schemas;

use OpenFGA\Exceptions\{ClientThrowable, SerializationException};

/**
 * Interface for schema validation and object transformation in the OpenFGA system.
 *
 * This interface defines the contract for validating raw data (typically from JSON API responses)
 * against registered schemas and transforming that data into properly typed OpenFGA model objects.
 * The validator ensures data integrity and type safety throughout the SDK.
 *
 * Schema validators maintain a registry of schemas and provide validation services for both
 * individual objects and collections. They handle complex validation scenarios including
 * nested objects, arrays, conditional properties, and custom format constraints.
 *
 * The transformation process creates fully initialized model objects with proper type
 * casting, default value handling, and constructor parameter mapping based on the
 * schema definitions.
 *
 * @see SchemaInterface The schemas that define validation rules
 * @see CollectionSchemaInterface Specialized schemas for collections
 * @see SerializationException For validation error details
 */
interface SchemaValidatorInterface
{
    /**
     * Get all currently registered schemas.
     *
     * Returns a comprehensive map of all schemas that have been registered with this
     * validator, keyed by their associated class names. This is useful for debugging,
     * introspection, and understanding which schemas are available for validation.
     *
     * @return array<string, SchemaInterface> Map of class names to their schema definitions
     */
    public function getSchemas(): array;

    /**
     * Register a schema for validation use.
     *
     * Adds a schema to the validator's registry, making it available for use in
     * validation and transformation operations. Schemas must be registered before
     * they can be used to validate data for their associated class.
     *
     * @param  SchemaInterface $schema The schema definition to register
     * @return self            Returns the validator instance for method chaining
     */
    public function registerSchema(SchemaInterface $schema): self;

    /**
     * Validate data against a registered schema and transform it into the target class instance.
     *
     * This method performs comprehensive validation of the provided data against the schema
     * for the specified class name. If validation succeeds, it creates and returns a fully
     * initialized instance of the target class with all data properly transformed and typed.
     *
     * The validation process includes:
     * - Required field validation
     * - Type checking and conversion
     * - Format validation (dates, enums, etc.)
     * - Nested object validation
     * - Collection validation for arrays
     * - Constructor parameter mapping
     * - Default value application
     *
     * @template T of object
     *
     * @param mixed           $data      The raw data to validate (typically an array from JSON)
     * @param class-string<T> $className The fully qualified class name to validate against
     *
     * @throws ClientThrowable If validation fails due to invalid data structure, missing required fields, type mismatches, or object creation failures
     *
     * @return T The validated and transformed object instance
     */
    public function validateAndTransform(mixed $data, string $className): object;
}
