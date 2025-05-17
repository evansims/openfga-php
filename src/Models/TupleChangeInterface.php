<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;

interface TupleChangeInterface extends ModelInterface
{
    public function getOperation(): TupleOperation;

    public function getTimestamp(): DateTimeImmutable;

    public function getTupleKey(): TupleKeyInterface;
}
