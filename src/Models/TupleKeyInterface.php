<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface TupleKeyInterface extends ModelInterface
{
    public function getCondition(): ?ConditionInterface;

    public function getObject(): ?string;

    public function getRelation(): ?string;

    public function getUser(): ?string;

    /**
     * @return array{user: string, relation: string, object: string, condition?: array<string, mixed>}
     */
    public function jsonSerialize(): array;
}
