<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use OpenFGA\Models\Enums\TupleOperation;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents a change to a relationship tuple in your authorization store.
 *
 * When you modify relationships in OpenFGA (adding or removing tuples), each change
 * is tracked as a TupleChange. This allows you to see the history of authorization
 * changes, audit permissions over time, and understand when relationships were
 * established or removed.
 *
 * Use this when you need to track or review the history of relationship changes
 * in your application, such as for compliance auditing or debugging permission issues.
 */
final class TupleChange implements TupleChangeInterface
{
    public const string OPENAPI_MODEL = 'TupleChange';

    private static ?SchemaInterface $schema = null;

    /**
     * Create a new tuple change record.
     *
     * @param TupleKeyInterface $tupleKey  The relationship tuple that was changed (user, relation, object)
     * @param TupleOperation    $operation Whether this was a WRITE (add) or DELETE (remove) operation
     * @param DateTimeImmutable $timestamp When this change occurred
     */
    public function __construct(
        private readonly TupleKeyInterface $tupleKey,
        private readonly TupleOperation $operation,
        private readonly DateTimeImmutable $timestamp,
    ) {
    }

    /**
     * Create TupleChange from array data.
     *
     * @param  array{tuple_key: TupleKeyInterface, operation: string, timestamp: DateTimeImmutable} $data The array data to convert
     * @return self                                                                                 The created TupleChange instance
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
     * Converts a DateTimeInterface to a UTC timestamp string in RFC3339 format.
     *
     * @param  DateTimeInterface $dateTime The datetime to convert
     * @return string            The UTC timestamp string in RFC3339 format
     */
    private static function getUtcTimestamp(DateTimeInterface $dateTime): string
    {
        return ($dateTime instanceof DateTimeImmutable ? $dateTime : DateTimeImmutable::createFromInterface($dateTime))
            ->setTimezone(new DateTimeZone('UTC'))->format(DateTimeInterface::RFC3339);
    }
}
