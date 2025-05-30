<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

use Override;

/**
 * JSON schema definition for validating and transforming data structures.
 *
 * This schema defines validation rules and transformation logic for converting
 * raw API response data into strongly-typed model objects. It specifies property
 * types, validation constraints, and mapping rules for accurate data processing.
 *
 * @see SchemaInterface For the complete API specification
 */
final class Schema implements SchemaInterface
{
    /**
     * @var array<string, SchemaProperty>
     */
    private array $properties = [];

    /**
     * Create a new schema definition for a specific class.
     *
     * @param string                $className  The fully qualified class name this schema defines
     * @param array<SchemaProperty> $properties Array of schema properties to include in this schema
     */
    public function __construct(
        public readonly string $className,
        array $properties = [],
    ) {
        foreach ($properties as $property) {
            $this->properties[$property->name] = $property;
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getProperty(string $name): ?SchemaProperty
    {
        return $this->properties[$name] ?? null;
    }
}
