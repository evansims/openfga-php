<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class Tuple implements TupleInterface
{
    public const OPENAPI_MODEL = 'Tuple';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly TupleKeyInterface $key,
        private readonly DateTimeImmutable $timestamp,
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
        return [
            'key' => $this->key->jsonSerialize(),
            'timestamp' => self::getUtcTimestamp($this->timestamp),
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

    private static function getUtcTimestamp(DateTimeInterface $dateTime): string
    {
        return ($dateTime instanceof DateTimeImmutable ? $dateTime : DateTimeImmutable::createFromInterface($dateTime))
            ->setTimezone(new DateTimeZone('UTC'))->format(DateTimeInterface::RFC3339);
    }
}
