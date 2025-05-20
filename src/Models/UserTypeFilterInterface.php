<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface UserTypeFilterInterface extends ModelInterface
{
    public function getRelation(): ?string;

    public function getType(): string;

    /**
     * @return array{type: string, relation?: string}
     */
    public function jsonSerialize(): array;
}
