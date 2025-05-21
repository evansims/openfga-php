<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use OpenFGA\Models\Enums\TupleOperation;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class TupleChange implements TupleChangeInterface
{
    public const OPENAPI_TYPE = 'TupleChange';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private TupleKeyInterface $tupleKey,
        private readonly TupleOperation $operation,
        private DateTimeImmutable $timestamp,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getOperation(): TupleOperation
    {
        return $this->operation;
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
    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'operation' => $this->operation->value,
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
                new SchemaProperty(name: 'tuple_key', type: TupleKey::class, required: true),
                new SchemaProperty(name: 'operation', type: TupleOperation::class, required: true),
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
