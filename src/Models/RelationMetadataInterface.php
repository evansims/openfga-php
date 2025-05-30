<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\RelationReferencesInterface;
use Override;

/**
 * Represents metadata associated with a relation in OpenFGA authorization models.
 *
 * Relation metadata provides additional context and constraints for relations
 * defined in type definitions. This metadata helps with:
 *
 * - Type safety by defining which user types can be directly related
 * - Development tooling by providing source file information
 * - Model organization through module names
 * - Validation and error reporting
 *
 * The metadata is particularly important for:
 * - Ensuring that only appropriate user types can be assigned to relations
 * - Providing helpful error messages when model validation fails
 * - Supporting development tools that work with authorization models
 * - Organizing complex models across multiple modules or files
 *
 * @see https://openfga.dev/docs/modeling/getting-started OpenFGA Modeling Guide
 * @see https://openfga.dev/docs/concepts#authorization-model Authorization Models
 */
interface RelationMetadataInterface extends ModelInterface
{
    /**
     * Get the user types that can be directly related through this relation.
     *
     * This defines which types of users can have this relation to objects,
     * providing type safety and helping with authorization model validation.
     * For example, a "member" relation might allow "user" and "group" types.
     *
     * @return RelationReferencesInterface<RelationReferenceInterface>|null The directly related user types, or null if not specified
     */
    public function getDirectlyRelatedUserTypes(): ?RelationReferencesInterface;

    /**
     * Get the optional module name for organization.
     *
     * This provides organizational information about which module or
     * namespace contains the relation definition, helping with model
     * organization and debugging.
     *
     * @return string|null The module name, or null if not specified
     */
    public function getModule(): ?string;

    /**
     * Get optional source file information for debugging and tooling.
     *
     * This provides information about the source file where the relation
     * was originally defined, which is useful for development tools,
     * debugging, and error reporting.
     *
     * @return SourceInfoInterface|null The source file information, or null if not available
     */
    public function getSourceInfo(): ?SourceInfoInterface;

    /**
     * @return array{module?: string, directly_related_user_types?: array<string, array{type: string, relation?: string, wildcard?: object, condition?: string}>, source_info?: array{file?: string}}
     */
    #[Override]
    public function jsonSerialize(): array;
}
