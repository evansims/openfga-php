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

    /**
     * @inheritDoc
     */
    #[Override]
    public function getOperation(): TupleOperation
    {
        return $this->operation;
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
    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'operation' => $this->operation->value,
            'timestamp' => self::getUtcTimestamp($this->timestamp),
        ];
    }

    /**
     * Create TupleChange from array data.
     *
     * @param array{tuple_key: TupleKeyInterface, operation: string, timestamp: DateTimeImmutable} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            tupleKey: $data['tuple_key'],
            operation: TupleOperation::from($data['operation']),
            timestamp: $data['timestamp'],
        );
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
                new SchemaProperty(name: 'tuple_key', type: 'object', className: TupleKey::class, required: true),
                new SchemaProperty(name: 'operation', type: 'string', required: true),
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
