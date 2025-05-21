<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface ObjectRelationInterface extends ModelInterface
{
    public function getObject(): ?string;

    public function getRelation(): ?string;

    /**
     * @return array{object?: string, relation?: string}
     */
    #[Override]
    public function jsonSerialize(): array;
}
