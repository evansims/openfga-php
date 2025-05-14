<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type ContinuationTokenShape = string
 */
interface ContinuationTokenInterface extends ModelInterface
{
    public function __toString(): string;

    public function getToken(): string;

    public function jsonSerialize(): string;
}
