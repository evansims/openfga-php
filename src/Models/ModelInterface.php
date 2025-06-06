<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use JsonSerializable;
use OpenFGA\Schemas\SchemaInterface;

/**
 * Base interface for all OpenFGA model objects.
 *
 * This interface establishes the foundation for all domain models in the OpenFGA SDK,
 * ensuring consistent behavior for serialization and schema validation across the entire
 * model hierarchy. All OpenFGA models implement this interface to provide uniform
 * JSON serialization capabilities and schema-based validation.
 *
 * Models in the OpenFGA ecosystem represent various authorization concepts:
 * - Authorization models that define permission structures
 * - Relationship tuples that establish actual relationships
 * - Stores that contain authorization data
 * - Users, objects, and conditions used in authorization decisions
 *
 * The schema system enables robust type checking, validation, and transformation
 * of data throughout the SDK, ensuring data integrity and API compatibility.
 *
 * @see https://openfga.dev/docs/concepts OpenFGA Core Concepts
 * @see SchemaInterface Schema validation system
 * @see JsonSerializable Standard PHP JSON serialization interface
 */
interface ModelInterface extends JsonSerializable
{
    /**
     * Get the schema definition for this model.
     *
     * This method returns the schema that defines the structure, validation rules, and serialization
     * behavior for this model class. The schema is used for data validation, transformation, and
     * ensuring consistency across API operations with the OpenFGA service.
     *
     * Each model's schema defines:
     * - Required and optional properties
     * - Data types and format constraints
     * - Nested object relationships
     * - Validation rules and business logic constraints
     *
     * The schema system enables the SDK to automatically validate incoming data,
     * transform between different representations, and ensure compliance with
     * the OpenFGA API specification.
     *
     * @return SchemaInterface The schema definition containing validation rules and property specifications for this model
     */
    public static function schema(): SchemaInterface;
}
