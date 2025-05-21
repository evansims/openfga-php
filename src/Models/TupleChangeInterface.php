<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use OpenFGA\Models\Enums\TupleOperation;
use Override;

interface TupleChangeInterface extends ModelInterface
{
    public function getOperation(): TupleOperation;

    public function getTimestamp(): DateTimeImmutable;

    public function getTupleKey(): TupleKeyInterface;

    /**
     * @return array{operation: 'TUPLE_OPERATION_DELETE'|'TUPLE_OPERATION_WRITE', timestamp: string, tuple_key: array<'condition'|'object'|'relation'|'user', array{expression: string, metadata?: array{module: string, source_info: array{file: string}}, name: string, parameters?: list<array{generic_types?: mixed, type_name: string}>}|string>}
     */
    #[Override]
    public function jsonSerialize(): array;
}
