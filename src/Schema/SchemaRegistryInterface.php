<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

/**
 * Registry for managing schema definitions in the OpenFGA system.
 *
 * This interface provides a centralized storage and retrieval system for schema objects,
 * enabling registration and lookup of schemas by class name. The registry serves as the
 * single source of truth for all schema definitions used throughout the OpenFGA SDK.
 *
 * The registry supports dynamic schema registration during runtime and provides factory
 * methods for creating new schema builders. This centralized approach ensures consistent
 * validation behavior across all OpenFGA model objects and API responses.
 *
 * @see SchemaBuilder Factory for creating new schemas
 * @see Schema The schema objects managed by this registry
 */
interface SchemaRegistryInterface
{
    /**
     * Create a new schema builder for the specified class.
     *
     * @param  class-string  $className The fully qualified class name
     * @return SchemaBuilder A new schema builder instance
     */
    public static function create(string $className): SchemaBuilder;

    /**
     * Retrieve a registered schema by class name.
     *
     * @param  class-string $className The fully qualified class name
     * @return ?Schema      The schema instance or null if not found
     */
    public static function get(string $className): ?Schema;

    /**
     * Register a schema in the registry.
     *
     * @param Schema $schema The schema instance to register
     */
    public static function register(Schema $schema): void;
}
