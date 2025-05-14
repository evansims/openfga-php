<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Assertions implements AssertionsInterface
{
    use CollectionTrait;

    public function add(AssertionInterface $assertion): void
    {
        $this->models[] = $assertion;
    }

    public function current(): AssertionInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?AssertionInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedAssertionsShape($data);
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(Assertion::fromArray($model));
        }

        return $collection;
    }

    /**
     * Validates the shape of the array to be used as assertions data. Throws an exception if the data is invalid.
     *
     * @param list<AssertionShape> $data
     *
     * @throws InvalidArgumentException
     *
     * @return AssertionsShape
     */
    public static function validatedAssertionsShape(array $data): array
    {
        return $data;
    }
}
