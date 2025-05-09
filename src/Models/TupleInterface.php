<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;

interface TupleInterface extends ModelInterface
{
    public function getKey(): TupleKeyInterface;

    public function getTimestamp(): DateTimeImmutable;
}
