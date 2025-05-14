<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use InvalidArgumentException;

use function is_array;
use function is_string;

final class Tuple implements TupleInterface
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

    public function jsonSerialize(): array
    {
        return [
            'key' => $this->getKey()->jsonSerialize(),
            'timestamp' => $this->getTimestamp()->format('Y-m-d\TH:i:s\Z'),
        ];
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedTupleShape($data);

        return new self(
            key: TupleKey::fromArray(TupleKeyType::GENERIC_TUPLE_KEY, $data['key']),
            timestamp: new DateTimeImmutable($data['timestamp']),
        );
    }

    /**
     * Validates the shape of a tuple. Throws an exception if the data is invalid.
     *
     * @param array{key: TupleKeyShape, timestamp: string} $data
     *
     * @throws InvalidArgumentException
     *
     * @return TupleShape
     */
    public static function validatedTupleShape(array $data): array
    {
        if (! isset($data['key']) || ! is_array($data['key'])) {
            throw new InvalidArgumentException('Tuple must have a key');
        }

        if (! isset($data['timestamp']) || ! is_string($data['timestamp'])) {
            throw new InvalidArgumentException('Tuple must have a timestamp');
        }

        return $data;
    }
}
