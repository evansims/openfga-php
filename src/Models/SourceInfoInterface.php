<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface SourceInfoInterface extends ModelInterface
{
    public function getFile(): string;

    /**
     * @return array{file: string}
     */
    #[Override]
    public function jsonSerialize(): array;
}
