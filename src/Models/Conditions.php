<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Conditions implements ConditionsInterface
{
    use CollectionTrait;

    public function add(ConditionInterface $condition): void
    {
        $this->models[] = $condition;
    }

    public function current(): ConditionInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?ConditionInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedConditions($data);
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(Condition::fromArray($model));
        }

        return $collection;
    }

    /**
     * Validates the shape of the array to be used as conditions data. Throws an exception if the data is invalid.
     *
     * @param array<int, ConditionShape> $data
     *
     * @throws InvalidArgumentException
     *
     * @return ConditionsShape
     */
    public static function validatedConditions(array $data): array
    {
        return $data;
    }
}
