<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

use Override;

/**
 * Centralized registry for managing schema definitions across the OpenFGA system.
 *
 * This registry provides a static, global repository for schema definitions that can be
 * accessed throughout the application lifecycle. It serves as the primary mechanism for
 * storing, retrieving, and creating schema definitions for OpenFGA model objects.
 *
 * The registry supports both programmatic schema creation through the builder pattern
 * and direct schema registration for pre-defined schemas. This centralized approach
 * ensures consistent schema validation across all model objects and eliminates the
 * need for redundant schema definitions.
 *
 * Schemas registered here are used by the SchemaValidator for object validation and
 * transformation during API response processing and data serialization operations.
 *
 * @see SchemaBuilder For creating new schema definitions
 * @see SchemaValidator For using registered schemas during validation
 * @see Schema For the schema definition structure
 */
final class SchemaRegistry implements SchemaRegistryInterface
{
    /**
     * @var array<string, Schema>
     */
    private static array $schemas = [];

    /**
     * @inheritDoc
     */
    #[Override]
    public static function create(string $className): SchemaBuilder
    {
        return new SchemaBuilder($className);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function get(string $className): ?Schema
    {
        return self::$schemas[$className] ?? null;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function register(Schema $schema): void
    {
        self::$schemas[$schema->className] = $schema;
    }
}
