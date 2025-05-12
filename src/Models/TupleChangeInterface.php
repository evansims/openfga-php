<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;

interface TupleChangeInterface extends ModelInterface
{
    public function getKey(): TupleKeyInterface;

    public function getOperation(): TupleOperation;

    public function getTimestamp(): DateTimeImmutable;
}
