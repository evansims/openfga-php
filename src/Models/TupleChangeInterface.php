<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use OpenFGA\Models\Enums\TupleOperation;

interface TupleChangeInterface extends ModelInterface
{
    public function getOperation(): TupleOperation;

    public function getTimestamp(): DateTimeImmutable;

    public function getTupleKey(): TupleKeyInterface;

    /**
     * @return array{
     *     tuple_key: array{
     *         user: string,
     *         relation: string,
     *         object: string,
     *         condition?: array<string, mixed>,
     *     },
     *     operation: string,
     *     timestamp: string,
     * }
     */
    public function jsonSerialize(): array;
}
