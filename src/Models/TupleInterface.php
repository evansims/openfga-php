<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use Override;

interface TupleInterface extends ModelInterface
{
    public function getKey(): TupleKeyInterface;

    public function getTimestamp(): DateTimeImmutable;

    /**
     * @return array{key: array<'condition'|'object'|'relation'|'user', array{expression: string, metadata?: array{module: string, source_info: array{file: string}}, name: string, parameters?: list<array{generic_types?: mixed, type_name: string}>}|string>, timestamp: string}
     */
    #[Override]
    public function jsonSerialize(): array;
}
