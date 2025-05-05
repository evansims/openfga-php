<?php

declare(strict_types=1);

namespace OpenFGA\Models;

abstract class Model implements ModelInterface
{
    final public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    abstract public function toArray(): array;

    abstract public static function fromArray(array $data): self;
}
