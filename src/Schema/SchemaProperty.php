<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

final class SchemaProperty
{
    /**
     * @param string                                             $name
     * @param string                                             $type
     * @param bool                                               $required
     * @param mixed                                              $default
     * @param null|string                                        $format
     * @param null|array<string>                                 $enum
     * @param null|array{type: string, className?: class-string} $items
     * @param null|class-string                                  $className
     */
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
