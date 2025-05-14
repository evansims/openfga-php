<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class TupleKeys implements TupleKeysInterface
{
    use CollectionTrait;

    public function add(TupleKeyInterface $tupleKey): void
    {
        $this->models[] = $tupleKey;
    }

    public function current(): TupleKeyInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?TupleKeyInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(TupleKeyType $type, array $data): self
    {
        $data = self::validatedTupleKeysShape($data);
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(TupleKey::fromArray($type, $model));
        }

        return $collection;
    }

    /**
     * Validates the shape of the array to be used as tuple keys data. Throws an exception if the data is invalid.
     *
     * @param array<int, TupleKeyShape> $data
     *
     * @throws InvalidArgumentException
     *
     * @return TupleKeysShape
     */
    public static function validatedTupleKeysShape(array $data): array
    {
        return $data;
    }
}
