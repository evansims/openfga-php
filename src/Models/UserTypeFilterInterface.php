<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface UserTypeFilterInterface extends ModelInterface
{
    public function getRelation(): ?string;

    public function getType(): string;

    /**
     * @return array<'relation'|'type', string>
     */
    #[Override]
    public function jsonSerialize(): array;
}
