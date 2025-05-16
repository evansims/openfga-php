<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

final class Schema implements SchemaInterface
{
    /**
     * @var array<string, SchemaProperty>
     */
    private array $properties = [];

    /**
     * @param array<SchemaProperty> $properties
     */
    public function __construct(
        public readonly string $className,
        array $properties = [],
    ) {
        foreach ($properties as $property) {
            $this->properties[$property->name] = $property;
        }
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getProperty(string $name): ?SchemaProperty
    {
        return $this->properties[$name] ?? null;
    }
}
