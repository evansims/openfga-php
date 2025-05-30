<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\{TupleKeysInterface, UserTypeFiltersInterface};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\{TupleKeyInterface, UserTypeFilterInterface};

/**
 * Interface for listing users who have a specific relation to an object.
 *
 * This interface defines the contract for requests that query which users
 * have a specific relationship to a given object. This is similar to the
 * expand operation but focuses specifically on returning the users rather
 * than the complete relationship graph structure.
 *
 * List users operations are particularly useful for:
 * - Building user interfaces that show who has access to a resource
 * - Implementing sharing and collaboration features
 * - Auditing and compliance reporting for access control
 * - Sending notifications to users with specific permissions
 * - Managing team membership and role assignments
 *
 * The operation supports:
 * - Filtering by user types to control result scope
 * - Contextual evaluation with additional runtime data
 * - Temporary relationship tuples for scenario testing
 * - Configurable read consistency levels for performance optimization
 * - Authorization model versioning for consistent results
 *
 * This provides the inverse perspective to list objects - instead of asking
 * "what can this user access?", it asks "who can access this object?".
 *
 * @see Consistency Read consistency options for balancing performance and accuracy
 * @see TupleKeyInterface Individual relationship tuple structure
 * @see TupleKeysInterface Collection of relationship tuples
 * @see UserTypeFilterInterface User type filtering configuration
 * @see UserTypeFiltersInterface Collection of user type filters
 * @see https://openfga.dev/docs/api/service#Relationship%20Queries/ListUsers OpenFGA List Users API Documentation
 */
interface ListUsersRequestInterface extends RequestInterface
{
    /**
     * Get the read consistency level for the list operation.
     *
     * Determines the consistency guarantees for reading authorization data
     * during the user listing operation. This allows you to balance between
     * read performance and data freshness based on your application's
     * requirements.
     *
     * @return Consistency|null The consistency level for the operation, or null to use the default consistency setting
     */
    public function getConsistency(): ?Consistency;

    /**
     * Get additional context data for conditional evaluation.
     *
     * Provides contextual information that can be used in conditional expressions
     * within the authorization model. This enables dynamic permission evaluation
     * based on runtime data such as time-based access, location restrictions,
     * or resource attributes when determining user access.
     *
     * @return object|null The context object containing additional data for evaluation, or null if no context is provided
     */
    public function getContext(): ?object;

    /**
     * Get additional tuples to consider during the list operation.
     *
     * Returns a collection of temporary relationship tuples that are added to
     * the authorization data during evaluation. This allows you to test access
     * scenarios with hypothetical or pending relationship changes without
     * permanently modifying the store.
     *
     * @return TupleKeysInterface<TupleKeyInterface>|null Additional relationship tuples for evaluation, or null if none provided
     */
    public function getContextualTuples(): ?TupleKeysInterface;

    /**
     * Get the authorization model ID to use for the list operation.
     *
     * Specifies which version of the authorization model should be used when
     * evaluating user access. Using a specific model ID ensures consistent
     * results even when the model is being updated.
     *
     * @return string The authorization model ID for evaluating user relationships
     */
    public function getModel(): string;

    /**
     * Get the object to list users for.
     *
     * Specifies the target object for which users will be listed. This
     * identifies the specific resource, document, or entity for which you
     * want to know which users have the specified relationship.
     *
     * @return string The object identifier to list users for
     */
    public function getObject(): string;

    /**
     * Get the relation to check for user access.
     *
     * Specifies the relationship type to evaluate when determining which users
     * have access to the object. For example, "owner", "editor", "viewer", or
     * "member". This defines what type of permission or relationship is being
     * queried.
     *
     * @return string The relation name to check for user access
     */
    public function getRelation(): string;

    /**
     * Get the store ID containing the authorization data.
     *
     * Identifies which OpenFGA store contains the relationship tuples and
     * configuration to use for the list operation. All evaluation will be
     * performed within the context of this specific store.
     *
     * @return string The store ID containing the authorization data
     */
    public function getStore(): string;

    /**
     * Get the user type filters to apply to results.
     *
     * Returns a collection of filters that control which types of users are
     * included in the results. This allows you to narrow the scope of the
     * query to specific user types, such as individual users, groups, or
     * service accounts, based on your application's needs.
     *
     * User filters help optimize performance and focus results by excluding
     * user types that are not relevant to the current operation.
     *
     * @return UserTypeFiltersInterface<UserTypeFilterInterface> Collection of user type filters to apply to the results
     */
    public function getUserFilters(): UserTypeFiltersInterface;
}
