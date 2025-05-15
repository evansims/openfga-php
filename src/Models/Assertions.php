<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

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
}
