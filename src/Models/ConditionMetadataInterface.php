<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface ConditionMetadataInterface extends ModelInterface
{
    public function getModule(): string;

    public function getSourceInfo(): SourceInfoInterface;

    /**
     * @return array{module: string, source_info: array{file: string}}
     */
    public function jsonSerialize(): array;
}
