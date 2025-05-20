<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;

interface TupleInterface extends ModelInterface
{
    public function getKey(): TupleKeyInterface;

    public function getTimestamp(): DateTimeImmutable;

    /**
     * @return array{key: array{user: string, relation: string, object: string, condition?: array<string, mixed>}, timestamp: string}
     */
    public function jsonSerialize(): array;
}
