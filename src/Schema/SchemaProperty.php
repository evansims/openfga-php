<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

final class SchemaProperty
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly bool $required = false,
        public readonly mixed $default = null,
        public readonly ?string $format = null,
        public readonly ?array $enum = null,
        public readonly ?array $items = null,
        public readonly ?string $className = null,
    ) {
    }
}
