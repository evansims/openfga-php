<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;

/**
 * @psalm-type TupleShape = array{'key': TupleKeyShape,'timestamp': string}
 */
interface TupleInterface extends ModelInterface
{
    public function getKey(): TupleKeyInterface;

    public function getTimestamp(): DateTimeImmutable;

    /**
     * @return TupleShape
     */
    public function jsonSerialize(): array;
}
