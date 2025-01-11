<?php

declare(strict_types=1);

namespace OpenFGA\API;

use IteratorAggregate;
use ArrayIterator;
use Countable;

final class TuplesCollection implements IteratorAggregate, Countable
{
    public function __construct(
        private Tuple ...$tuples,
    ) {
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->tuples);
    }

    public function count(): int
    {
        return count($this->tuples);
    }
}
