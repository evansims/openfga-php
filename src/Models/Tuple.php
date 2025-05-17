<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use DateTimeZone;
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
        $timestamp = $this->getTimestamp();

        $utcTimestamp = 0 === $timestamp->getOffset()
            ? $timestamp
            : $timestamp->setTimezone(new DateTimeZone('UTC'));

        return [
            'key' => $this->getKey()->jsonSerialize(),
            'timestamp' => (
                0 === $timestamp->getOffset()
                    ? $timestamp
                    : $timestamp->setTimezone(new DateTimeZone('UTC'))
            )->format(DATE_ATOM),
        ];
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
