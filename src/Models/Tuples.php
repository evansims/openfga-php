<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class Tuples implements TuplesInterface
{
    use CollectionTrait;

    public function add(TupleInterface $tuple): void
    {
        $this->models[] = $tuple;
    }

    public function current(): TupleInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?TupleInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedTuplesShape($data);
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(Tuple::fromArray($model));
        }

        return $collection;
    }

    /**
     * Validates the shape of the array to be used as tuple data. Throws an exception if the data is invalid.
     *
     * @param list<TupleShape> $data
     *
     * @throws InvalidArgumentException
     *
     * @return TuplesShape
     */
    public static function validatedTuplesShape(array $data): array
    {
        return $data;
    }
}
