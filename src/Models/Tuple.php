<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;

use function assert;
use function is_array;
use function is_string;

final class Tuple extends Model implements TupleInterface
{
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

    public function toArray(): array
    {
        return [
            'key' => $this->key->toArray(),
            'timestamp' => $this->timestamp->format('Y-m-d\TH:i:s\Z'),
        ];
    }

    public static function fromArray(array $data): self
    {
        assert(isset($data['key']) && is_array($data['key']));
        assert(isset($data['timestamp']) && is_string($data['timestamp']));

        return new self(
            key: TupleKey::fromArray($data['key']),
            timestamp: new DateTimeImmutable($data['timestamp']),
        );
    }
}
