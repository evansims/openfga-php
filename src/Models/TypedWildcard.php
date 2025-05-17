<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class TypedWildcard implements TypedWildcardInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private string $type,
    ) {
        $type = strtolower(trim($type));

        if ('' === $type) {
            throw new InvalidArgumentException('TypedWildcard::$type cannot be empty.');
        }

        $this->type = $type;
    }

    public function __toString(): string
    {
        return $this->getType();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->getType(),
        ];
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'type', type: 'string', required: true),
            ],
        );
    }
}
