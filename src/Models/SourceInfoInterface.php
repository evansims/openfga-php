<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use JsonSerializable;

interface SourceInfoInterface extends ModelInterface, JsonSerializable
{
    public function getFile(): string;

    /**
     * @return array{file: string}
     */
    public function jsonSerialize(): array;

    /**
     * @param array{file: string} $data
     */
    public static function fromArray(array $data): static;
}
