<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type DifferenceShape = array{
 *     base: UsersetShape,
 *     subtract: UsersetShape,
 * }
 */
interface DifferenceV1Interface extends ModelInterface
{
    /**
     * @return UsersetInterface
     */
    public function getBase(): UsersetInterface;

    /**
     * @return UsersetInterface
     */
    public function getSubtract(): UsersetInterface;

    /**
     * @return DifferenceShape
     */
    public function jsonSerialize(): array;

    /**
     * @param DifferenceShape $data
     */
    public static function fromArray(array $data): self;
}
