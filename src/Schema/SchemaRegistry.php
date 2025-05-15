<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

final class SchemaRegistry
{
    /**
     * @var array<string, Schema>
     */
    private static array $schemas = [];

    /**
     * Create a schema builder.
     *
     * @param class-string $className
     */
    public static function create(string $className): SchemaBuilder
    {
        return new SchemaBuilder($className);
    }

    /**
     * Get a schema by class name.
     *
     * @param class-string $className
     */
    public static function get(string $className): ?Schema
    {
        return self::$schemas[$className] ?? null;
    }

    /**
     * Register a schema.
     *
     * @param Schema $schema
     */
    public static function register(Schema $schema): void
    {
        self::$schemas[$schema->className] = $schema;
    }
}
