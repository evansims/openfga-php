<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface ComputedInterface extends ModelInterface
{
    public function getUserset(): string;

    /**
     * @return array{userset: string}
     */
    public function jsonSerialize(): array;
}
