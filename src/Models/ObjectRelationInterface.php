<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type ObjectRelationShape = array{object?: string, relation?: string}
 */
interface ObjectRelationInterface extends ModelInterface
{
    public function getObject(): ?string;

    public function getRelation(): ?string;

    /**
     * @return ObjectRelationShape
     */
    public function jsonSerialize(): array;
}
