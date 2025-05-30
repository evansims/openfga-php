<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;

/**
 * Interface for listing objects that a user has access to.
 *
 * This interface defines the contract for requests that query which objects
 * a specific user can access for a given relation. This is the inverse of
 * a permission check - instead of asking "can this user access this object?",
 * it asks "what objects can this user access?".
 *
 * List objects operations support:
 * - Filtering by object type and relation
 * - Contextual evaluation with additional data
 * - Temporary relationship tuples for scenario testing
 * - Configurable read consistency levels
 * - Authorization model versioning for consistent results
 *
 * This is particularly useful for building user interfaces that need to
 * display only the resources a user can access, such as file listings,
 * document repositories, or administrative dashboards.
 *
 * @see Consistency Read consistency options for balancing performance and accuracy
 * @see TupleKeyInterface Individual relationship tuple structure
 * @see TupleKeysInterface Collection of relationship tuples
 * @see https://openfga.dev/docs/api/service#Relationship%20Queries/ListObjects OpenFGA List Objects API Documentation
 */
interface ListObjectsRequestInterface extends RequestInterface
{
    /**
     * Get the read consistency level for the list operation.
     *
     * Determines the consistency guarantees for reading authorization data
     * during the list operation. This allows you to balance between read
     * performance and data freshness based on your application's requirements.
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
     * or resource attributes.
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
     * evaluating object access. Using a specific model ID ensures consistent
     * results even when the model is being updated. If not specified, the
     * latest model version will be used.
     *
     * @return string|null The authorization model ID for evaluation, or null to use the latest model version
     */
    public function getModel(): ?string;

    /**
     * Get the relation to check for object access.
     *
     * Specifies the relationship type to evaluate when determining object access.
     * For example, "can_view", "can_edit", or "owner". This defines what type
     * of permission or relationship is being queried.
     *
     * @return string The relation name to check for object access
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
     * Get the object type to filter results by.
     *
     * Specifies the type of objects to include in the results. Only objects
     * of this type will be considered when determining what the user can access.
     * For example, "document", "folder", or "repository".
     *
     * @return string The object type to filter results by
     */
    public function getType(): string;

    /**
     * Get the user to check object access for.
     *
     * Identifies the user for whom object access is being evaluated. This can
     * be a direct user identifier or a userset expression. The operation will
     * return all objects of the specified type that this user can access
     * through the specified relation.
     *
     * @return string The user identifier or userset to check object access for
     */
    public function getUser(): string;
}
