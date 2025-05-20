<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface ObjectRelationInterface extends ModelInterface
{
    public function getObject(): ?string;

    public function getRelation(): ?string;

    /**
     * @return array{object?: string, relation?: string}
     */
    public function jsonSerialize(): array;
}
