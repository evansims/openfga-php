<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class Tuple implements TupleInterface
{
    public const string OPENAPI_MODEL = 'Tuple';

    private static ?SchemaInterface $schema = null;

    /**
     * @param TupleKeyInterface $key       The tuple key containing user, relation, and object
     * @param DateTimeImmutable $timestamp The timestamp when the tuple was created
     */
    public function __construct(
        private readonly TupleKeyInterface $key,
        private readonly DateTimeImmutable $timestamp,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'key', type: 'object', className: TupleKey::class, required: true),
                new SchemaProperty(name: 'timestamp', type: 'string', format: 'datetime', required: true),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getKey(): TupleKeyInterface
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key->jsonSerialize(),
            'timestamp' => self::getUtcTimestamp($this->timestamp),
        ];
    }

    /**
     * Converts a DateTimeInterface to a UTC timestamp string in RFC3339 format.
     *
     * @param  DateTimeInterface $dateTime The datetime to convert
     * @return string            The UTC timestamp string
     */
    private static function getUtcTimestamp(DateTimeInterface $dateTime): string
    {
        return ($dateTime instanceof DateTimeImmutable ? $dateTime : DateTimeImmutable::createFromInterface($dateTime))
            ->setTimezone(new DateTimeZone('UTC'))->format(DateTimeInterface::RFC3339);
    }
}
