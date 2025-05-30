<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\{ConditionsInterface, TypeDefinitionsInterface};
use OpenFGA\Models\{ConditionInterface, TypeDefinitionInterface};
use OpenFGA\Models\Enums\SchemaVersion;

/**
 * Interface for creating new authorization models in OpenFGA.
 *
 * This interface defines the contract for requests that create new authorization
 * models within an OpenFGA store. Authorization models define the relationship
 * types, object types, and access control rules that govern how permissions
 * are evaluated in your application.
 *
 * An authorization model consists of:
 * - **Type definitions**: Define object types and their allowed relationships
 * - **Conditions**: Define conditional logic for dynamic authorization
 * - **Schema version**: Specifies the model definition language version
 *
 * Authorization models are versioned, allowing you to evolve your permission
 * system over time while maintaining compatibility. Each new model receives
 * a unique ID that can be used to ensure consistent permission evaluation
 * even as the model evolves.
 *
 * Key capabilities include:
 * - Defining object types (documents, folders, organizations, etc.)
 * - Specifying relationship types (owner, editor, viewer, member, etc.)
 * - Creating inheritance and permission hierarchies
 * - Implementing conditional authorization with runtime context
 * - Supporting complex authorization patterns like RBAC, ABAC, and ReBAC
 *
 * @see TypeDefinitionInterface Individual object type definition
 * @see TypeDefinitionsInterface Collection of type definitions
 * @see ConditionInterface Individual conditional rule
 * @see ConditionsInterface Collection of conditional rules
 * @see SchemaVersion Authorization model schema version
 * @see https://openfga.dev/docs/api/service#Authorization%20Models/WriteAuthorizationModel OpenFGA Authorization Model API Documentation
 * @see https://openfga.dev/docs/modeling OpenFGA Authorization Modeling Guide
 */
interface CreateAuthorizationModelRequestInterface extends RequestInterface
{
    /**
     * Get the conditional rules for the authorization model.
     *
     * Returns a collection of conditions that define dynamic authorization
     * logic based on runtime context. Conditions allow for sophisticated
     * access control scenarios such as time-based access, location restrictions,
     * resource attributes, or custom business logic.
     *
     * Conditions are referenced by name within type definitions and evaluated
     * at permission check time using contextual data provided in authorization
     * requests. They enable attribute-based access control (ABAC) patterns
     * within the relationship-based authorization framework.
     *
     * @return ConditionsInterface<ConditionInterface>|null Collection of conditional rules for dynamic authorization, or null if no conditions are defined
     */
    public function getConditions(): ?ConditionsInterface;

    /**
     * Get the schema version for the authorization model.
     *
     * Specifies which version of the OpenFGA modeling language should be used
     * to interpret the authorization model definition. Different schema versions
     * support different features and syntax, allowing OpenFGA to evolve while
     * maintaining backward compatibility.
     *
     * The schema version determines:
     * - Available relationship operators and syntax
     * - Supported conditional expression features
     * - Type definition validation rules
     * - API compatibility and behavior
     *
     * @return SchemaVersion The modeling language schema version for this authorization model
     */
    public function getSchemaVersion(): SchemaVersion;

    /**
     * Get the store ID where the authorization model will be created.
     *
     * Identifies the OpenFGA store that will contain the new authorization
     * model. Each store can have multiple model versions, allowing you to
     * evolve your authorization schema over time while maintaining access
     * to previous versions for consistency and rollback scenarios.
     *
     * @return string The store ID where the authorization model will be created
     */
    public function getStore(): string;

    /**
     * Get the type definitions for the authorization model.
     *
     * Returns a collection of type definitions that specify the object types
     * and their allowed relationships within the authorization model. Type
     * definitions form the core schema that defines what objects exist in
     * your system and how they can be related to users and other objects.
     *
     * Each type definition includes:
     * - Object type name (e.g., "document", "folder", "organization")
     * - Allowed relationships (e.g., "owner", "editor", "viewer")
     * - Relationship inheritance and computation rules
     * - References to conditional logic for dynamic authorization
     *
     * @return TypeDefinitionsInterface<TypeDefinitionInterface> Collection of object type definitions that define the authorization schema
     */
    public function getTypeDefinitions(): TypeDefinitionsInterface;
}
