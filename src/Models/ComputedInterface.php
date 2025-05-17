<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type ComputedShape = array{userset: string}
 */
interface ComputedInterface extends ModelInterface
{
    public function getUserset(): string;

    /**
     * @return ComputedShape
     */
    public function jsonSerialize(): array;
}
