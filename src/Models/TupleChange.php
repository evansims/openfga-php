<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;

final class TupleChange extends Model implements TupleChangeInterface
{
    public function __construct(
        public TupleKeyInterface $tupleKey,
        public TupleOperation $operation,
        public DateTimeImmutable $timestamp,
    ) {
    }

    public function toArray(): array
    {
        return [
            'tuple_key' => $this->tupleKey->toArray(),
            'operation' => $this->operation->value,
            'timestamp' => $this->timestamp->format('Y-m-d\TH:i:sP'),
        ];
    }

    public static function fromArray(array $data): self
    {
        assert(isset($data['tuple_key']) && is_array($data['tuple_key']));
        assert(isset($data['operation']) && is_string($data['operation']));
        assert(isset($data['timestamp']) && is_string($data['timestamp']));

        return new self(
            tupleKey: TupleKey::fromArray($data['tuple_key']),
            operation: TupleOperation::from($data['operation']),
            timestamp: new DateTimeImmutable($data['timestamp']),
        );
    }
}
