<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface TypedWildcardInterface extends ModelInterface
{
    public function __toString(): string;

    public function getType(): string;

    /**
     * @return array{type: string}
     */
    #[Override]
    public function jsonSerialize(): array;
}
