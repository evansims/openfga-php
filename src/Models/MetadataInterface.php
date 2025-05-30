<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\RelationMetadataCollection;
use Override;

/**
 * Represents metadata associated with OpenFGA authorization model components.
 *
 * Metadata provides additional context and configuration information for
 * authorization model elements. This includes module organization,
 * relation-specific metadata, and source code information for debugging
 * and development purposes.
 *
 * Metadata helps with model organization, documentation, and tooling support
 * for complex authorization models.
 */
interface MetadataInterface extends ModelInterface
{
    /**
     * Get the module name for this metadata.
     *
     * Modules provide a way to organize and namespace authorization model
     * components, similar to packages in programming languages.
     * This helps with model organization and prevents naming conflicts
     * in large authorization systems.
     *
     * @return string|null The module name, or null if not specified
     */
    public function getModule(): ?string;

    /**
     * Get the collection of relation metadata.
     *
     * Relation metadata provides additional configuration and context
     * for specific relations within a type definition. This can include
     * documentation, constraints, or other relation-specific settings
     * that enhance the authorization model.
     *
     * @return RelationMetadataCollection|null The relation metadata collection, or null if not specified
     */
    public function getRelations(): ?RelationMetadataCollection;

    /**
     * Get the source code information for this metadata.
     *
     * Source information provides debugging and development context
     * by tracking where authorization model elements were defined.
     * This is particularly useful for development tools and error reporting.
     *
     * @return SourceInfoInterface|null The source information, or null if not available
     */
    public function getSourceInfo(): ?SourceInfoInterface;

    /**
     * @return array{module?: string, relations?: array<string, mixed>, source_info?: array{file?: string}}
     */
    #[Override]
    public function jsonSerialize(): array;
}
