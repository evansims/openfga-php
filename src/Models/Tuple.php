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
    public const OPENAPI_MODEL = 'Tuple';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly TupleKeyInterface $key,
        private readonly DateTimeImmutable $timestamp,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getKey(): TupleKeyInterface
    {
        return $this->key;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key->jsonSerialize(),
            'timestamp' => self::getUtcTimestamp($this->timestamp),
        ];
    }

    #[Override]
    /**
     * @inheritDoc
     */
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

    private static function getUtcTimestamp(DateTimeInterface $dateTime): string
    {
        return ($dateTime instanceof DateTimeImmutable ? $dateTime : DateTimeImmutable::createFromInterface($dateTime))
            ->setTimezone(new DateTimeZone('UTC'))->format(DateTimeInterface::RFC3339);
    }
}
