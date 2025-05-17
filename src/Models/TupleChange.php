<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use DateTimeZone;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class TupleChange implements TupleChangeInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private TupleKeyInterface $tupleKey,
        private TupleOperation $operation,
        private DateTimeImmutable $timestamp,
    ) {
    }

    public function getOperation(): TupleOperation
    {
        return $this->operation;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }

    public function jsonSerialize(): array
    {
        $timestamp = $this->getTimestamp();

        $utcTimestamp = 0 === $timestamp->getOffset()
            ? $timestamp
            : $timestamp->setTimezone(new DateTimeZone('UTC'));

        return [
            'tuple_key' => $this->getTupleKey()->jsonSerialize(),
            'operation' => $this->getOperation()->value,
            'timestamp' => $utcTimestamp->format(DATE_ATOM),
        ];
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'tuple_key', type: TupleKey::class, required: true),
                new SchemaProperty(name: 'operation', type: TupleOperation::class, required: true),
                new SchemaProperty(name: 'timestamp', type: 'string', format: 'date-time', required: true),
            ],
        );
    }
}
