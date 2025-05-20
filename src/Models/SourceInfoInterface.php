<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface SourceInfoInterface extends ModelInterface
{
    public function getFile(): string;

    /**
     * @return array{file: string}
     */
    public function jsonSerialize(): array;
}
