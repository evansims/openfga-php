<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Assertions extends ModelCollection implements AssertionsInterface
{
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
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(Assertion::fromArray($model));
        }

        return $collection;
    }
}
