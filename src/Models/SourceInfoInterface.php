<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type SourceInfoShape = array{file: string}
 */
interface SourceInfoInterface extends ModelInterface
{
    public function getFile(): string;

    /**
     * @return SourceInfoShape
     */
    public function jsonSerialize(): array;

    /**
     * @param SourceInfoShape $data
     */
    public static function fromArray(array $data): static;
}
