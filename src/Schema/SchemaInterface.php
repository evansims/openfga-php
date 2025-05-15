<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

interface SchemaInterface
{
    public function getClassName(): string;

    /**
     * @return array<string, SchemaProperty>
     */
    public function getProperties(): array;

    public function getProperty(string $name): ?SchemaProperty;
}
