<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class Tuple implements TupleInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private TupleKeyInterface $key,
        private DateTimeImmutable $timestamp,
    ) {
    }

    public function getKey(): TupleKeyInterface
    {
        return $this->key;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'key' => $this->key->jsonSerialize(),
            'timestamp' => $this->timestamp->format(DATE_ATOM),
        ], static fn ($value) => null !== $value);
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'key', type: TupleKey::class, required: true),
                new SchemaProperty(name: 'timestamp', type: 'string', format: 'date-time', required: true),
            ],
        );
    }
}
