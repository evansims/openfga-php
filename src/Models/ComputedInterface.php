<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface ComputedInterface extends ModelInterface
{
    public function getUserset(): string;

    /**
     * @return array{userset: string}
     */
    #[Override]
    public function jsonSerialize(): array;
}
