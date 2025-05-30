<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface ConditionMetadataInterface extends ModelInterface
{
    /**
     * Get the module name where the condition is defined.
     *
     * This provides organizational information about which module or
     * namespace contains the condition definition, helping with debugging
     * and understanding the model structure.
     *
     * @return string The module name containing the condition
     */
    public function getModule(): string;

    /**
     * Get source file information for debugging and tooling.
     *
     * This provides information about the source file where the condition
     * was originally defined, which is useful for development tools,
     * debugging, and error reporting.
     *
     * @return SourceInfoInterface The source file information
     */
    public function getSourceInfo(): SourceInfoInterface;

    /**
     * @return array{module: string, source_info: array{file: string}}
     */
    #[Override]
    public function jsonSerialize(): array;
}
