<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type DifferenceShape = array{base: UsersetShape, subtract: UsersetShape}
 */
interface DifferenceV1Interface extends ModelInterface
{
    public function getBase(): UsersetInterface;

    public function getSubtract(): UsersetInterface;

    /**
     * @return DifferenceShape
     */
    public function jsonSerialize(): array;
}
