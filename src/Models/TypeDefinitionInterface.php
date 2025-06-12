<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\TypeDefinitionRelationsInterface;
use Override;

/**
 * Represents a type definition in an OpenFGA authorization model.
 *
 * Type definitions are the building blocks of authorization models that define
 * the types of objects in your system and the relationships that can exist
 * between them. Each type definition specifies:
 *
 * - The type name (for example "document," "user," "organization")
 * - The relations that objects of this type can have (for example "viewer," "editor," "owner")
 * - Optional metadata for additional context and configuration
 *
 * Type definitions form the schema that OpenFGA uses to understand your
 * permission model and validate authorization queries.
 *
 * @see https://openfga.dev/docs/concepts#authorization-model OpenFGA Authorization Models
 * @see https://openfga.dev/docs/modeling/getting-started OpenFGA Modeling Guide
 */
interface TypeDefinitionInterface extends ModelInterface
{
    /**
     * Get the metadata associated with this type definition.
     *
     * Metadata provides additional context, documentation,
     * and configuration information for the type definition.
     * This can include source file information, module details,
     * and other development-time context.
     *
     * @return MetadataInterface|null The metadata, or null if not specified
     */
    public function getMetadata(): ?MetadataInterface;

    /**
     * Get the collection of relations defined for this type.
     *
     * Relations define the authorized relationships that can exist
     * between objects of this type and other entities in the system.
     */
    public function getRelations(): ?TypeDefinitionRelationsInterface;

    /**
     * Get the name of this type.
     *
     * The type name uniquely identifies this type definition
     * within the authorization model. Common examples include
     * "user," "document," "folder," "organization," etc.
     *
     * @return string The unique type name
     */
    public function getType(): string;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array;
}
