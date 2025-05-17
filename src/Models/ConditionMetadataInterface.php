<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type ConditionMetadataShape = array{module: string, source_info: SourceInfoShape}
 */
interface ConditionMetadataInterface extends ModelInterface
{
    public function getModule(): string;

    public function getSourceInfo(): SourceInfoInterface;

    /**
     * @return ConditionMetadataShape
     */
    public function jsonSerialize(): array;
}
