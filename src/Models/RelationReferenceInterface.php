<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface RelationReferenceInterface extends ModelInterface
{
    public function getCondition(): ?string;

    public function getRelation(): ?string;

    public function getType(): string;

    public function getWildcard(): ?object;

    /**
     * @return array{type: string, relation?: string, wildcard?: object, condition?: string}
     */
    public function jsonSerialize(): array;
}
