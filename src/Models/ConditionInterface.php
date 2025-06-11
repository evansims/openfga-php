<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\ConditionParametersInterface;
use Override;

/**
 * Represents a condition that enables dynamic authorization in OpenFGA.
 *
 * Conditions allow OpenFGA to make authorization decisions based on runtime context
 * and parameters, enabling attribute-based access control (ABAC) patterns. Rather
 * than relying solely on static relationships, conditions evaluate expressions
 * against dynamic data to determine if access should be granted.
 *
 * Conditions consist of:
 * - **Expression**: A logical expression that evaluates to true or false
 * - **Parameters**: Typed parameters that can be passed at evaluation time
 * - **Name**: A unique identifier for referencing the condition
 * - **Metadata**: Optional information about the condition definition
 *
 * Common condition use cases:
 * - Time-based access (business hours, expiration dates)
 * - Location-based restrictions (IP address, geographic region)
 * - Resource attributes (document classification, owner validation)
 * - User context (department, clearance level, current project)
 * - Environmental factors (device type, authentication method)
 *
 * Conditions are defined in authorization models and can be referenced by
 * relationship tuples to create dynamic permission rules. When OpenFGA
 * evaluates a conditional relationship, it passes the current context
 * parameters to the condition expression for evaluation.
 *
 * This enables sophisticated authorization patterns like "allow read access
 * to documents during business hours" or "grant edit permissions only to
 * users in the same department as the resource owner."
 *
 * @see https://openfga.dev/docs/modeling/conditions OpenFGA Conditions Documentation
 * @see https://openfga.dev/docs/concepts#what-is-a-condition Condition Concepts
 * @see https://openfga.dev/docs/configuration-language#conditions DSL Condition Syntax
 */
interface ConditionInterface extends ModelInterface
{
    /**
     * Get the condition expression.
     *
     * This returns the logical expression that defines when this condition evaluates to true.
     * The expression can reference parameters and context data to enable dynamic authorization
     * decisions based on runtime information.
     *
     * @return string The condition expression defining the evaluation logic
     */
    public function getExpression(): string;

    /**
     * Get metadata about the condition definition.
     *
     * This provides additional information about where the condition was defined and
     * how it should be processed, which is useful for tooling and debugging.
     *
     * @return ConditionMetadataInterface|null The condition metadata, or null if not provided
     */
    public function getMetadata(): ?ConditionMetadataInterface;

    /**
     * Get the name of the condition.
     *
     * This is a unique identifier for the condition within the authorization model,
     * allowing it to be referenced from type definitions and other parts of the model.
     *
     * @return string The unique name identifying this condition
     */
    public function getName(): string;

    /**
     * Get the parameters available to the condition expression.
     *
     * These parameters define the typed inputs that can be used within the condition
     * expression, enabling dynamic evaluation based on contextual data provided
     * during authorization checks.
     *
     * @return ConditionParametersInterface|null The condition parameters, or null if the condition uses no parameters
     */
    public function getParameters(): ?ConditionParametersInterface;

    /**
     * Get the context for the condition.
     *
     * This provides additional runtime data that can be used by the condition's
     * expression for dynamic evaluation.
     *
     * @return array<string, mixed>|null The context data, or null if not provided.
     */
    public function getContext(): ?array;

    /**
     * Serialize the condition for JSON encoding.
     *
     * This method prepares the condition data for API requests or storage, ensuring
     * all components are properly formatted according to the OpenFGA API specification.
     *
     * @return array<string, mixed> The serialized condition data
     */
    #[Override]
    public function jsonSerialize(): array;
}
