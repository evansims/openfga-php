<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;

/**
 * @psalm-type TupleChangeShape = array{'tuple_key': TupleKeyShape, 'operation': string, 'timestamp': string}
 *
 * @extends ModelInterface<TupleChangeShape>
 */
interface TupleChangeInterface extends ModelInterface
{
    public function getOperation(): TupleOperation;

    public function getTimestamp(): DateTimeImmutable;

    public function getTupleKey(): TupleKeyInterface;

    /**
     * @return TupleChangeShape
     */
    public function jsonSerialize(): array;
}
