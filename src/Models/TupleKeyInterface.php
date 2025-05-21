<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface TupleKeyInterface extends ModelInterface
{
    public function getCondition(): ?ConditionInterface;

    public function getObject(): ?string;

    public function getRelation(): ?string;

    public function getUser(): ?string;

    /**
     * @return array<'condition'|'object'|'relation'|'user', array{expression: string, metadata?: array{module: string, source_info: array{file: string}}, name: string, parameters?: list<array{generic_types?: mixed, type_name: string}>}|string>
     */
    #[Override]
    public function jsonSerialize(): array;
}
