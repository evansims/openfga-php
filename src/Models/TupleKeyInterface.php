<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Represents a tuple key that defines the components of a relationship in OpenFGA.
 *
 * Tuple keys are the core data structure that defines relationships in the OpenFGA
 * authorization system. They specify the essential components that together describe
 * an authorization relationship: who (user), what (relation), and where (object),
 * with optional conditional logic (condition).
 *
 * The tuple key structure follows the pattern:
 * - **User**: The subject of the relationship (who has the permission)
 * - **Relation**: The type of permission or relationship being defined
 * - **Object**: The resource or entity the permission applies to
 * - **Condition**: Optional runtime constraints that must be satisfied
 *
 * Examples of tuple keys:
 * - `user:alice` has `editor` relation to `document:readme`
 * - `group:engineering` has `member` relation to `user:bob`
 * - `user:contractor` has `read` relation to `file:confidential` when `time_constraint` is met
 *
 * Tuple keys are used throughout OpenFGA operations:
 * - Writing relationships (creating authorization facts)
 * - Reading relationships (querying existing permissions)
 * - Authorization checks (evaluating access requests)
 * - Relationship expansion (understanding permission inheritance)
 *
 * The flexible tuple key design enables OpenFGA to represent complex authorization
 * patterns while maintaining efficient query performance and clear relationship semantics.
 *
 * @see https://openfga.dev/docs/concepts#what-is-a-tuple-key OpenFGA Tuple Key Concepts
 * @see https://openfga.dev/docs/concepts#relationship-tuples Relationship Tuples Documentation
 * @see https://openfga.dev/docs/modeling/conditions Conditional Relationship Modeling
 */
interface TupleKeyInterface extends ModelInterface
{
    /**
     * Get the condition that constrains this relationship.
     *
     * Conditions enable dynamic authorization by allowing relationships to be conditional
     * based on runtime context, such as time of day, resource attributes, or other factors.
     * When a condition is present, the relationship is only valid when the condition evaluates to true.
     *
     * @return ConditionInterface|null The condition that must be satisfied for this relationship to be valid, or null for an unconditional relationship
     */
    public function getCondition(): ?ConditionInterface;

    /**
     * Get the object in this relationship tuple.
     *
     * The object represents the resource or entity that the permission or relationship applies to.
     * For example, in "user:alice can view document:readme," the object would be "document:readme."
     * Objects are typically formatted as "type:id" where type describes the kind of resource.
     *
     * @return string The object identifier
     */
    public function getObject(): string;

    /**
     * Get the relation that defines the type of relationship.
     *
     * The relation describes what kind of permission or relationship exists between the user and object.
     * For example, common relations include "owner," "viewer," "editor," "can_read," "can_write."
     * Relations are defined in your authorization model and determine what actions are permitted.
     *
     * @return string The relation name defining the type of relationship
     */
    public function getRelation(): string;

    /**
     * Get the user (subject) in this relationship tuple.
     *
     * The user represents the entity that has the relationship to the object. This can be
     * an individual user, a group, a role, or any other subject defined in your authorization model.
     * For example, in "user:alice can view document:readme," the user would be "user:alice."
     *
     * @return string The user identifier
     */
    public function getUser(): string;

    /**
     * Serialize the tuple key for JSON encoding.
     *
     * This method prepares the tuple key data for API requests or storage, ensuring all
     * components (user, relation, object, and optional condition) are properly formatted
     * according to the OpenFGA API specification.
     *
     * @return array<string, mixed> The serialized tuple key data ready for JSON encoding
     */
    #[Override]
    public function jsonSerialize(): array;
}
