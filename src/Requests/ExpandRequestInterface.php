<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;

/**
 * Interface for expanding relationship graphs in OpenFGA.
 *
 * This interface defines the contract for requests that expand authorization
 * relationships to show the complete graph of users that have access to a
 * resource through various relationship paths. The expand operation traces
 * all possible authorization paths and returns a tree structure showing
 * how permissions are derived.
 *
 * Expand operations are particularly useful for:
 * - Understanding complex authorization chains and inheritance
 * - Debugging permission issues and unexpected access grants
 * - Auditing who has access to sensitive resources and why
 * - Visualizing the authorization graph for administrative purposes
 * - Analyzing the impact of relationship changes before applying them
 *
 * The expansion can include direct relationships, inherited permissions,
 * and computed relationships through complex authorization model rules.
 *
 * @see Consistency Read consistency options for balancing performance and accuracy
 * @see TupleKeyInterface Individual relationship tuple structure
 * @see TupleKeysInterface Collection of relationship tuples
 * @see https://openfga.dev/docs/api/service#Relationship%20Queries/Expand OpenFGA Expand API Documentation
 */
interface ExpandRequestInterface extends RequestInterface
{
    /**
     * Get the read consistency level for the expand operation.
     *
     * Determines the consistency guarantees for reading authorization data
     * during the expansion. This allows you to balance between read performance
     * and data freshness based on your application's requirements.
     *
     * @return Consistency|null The consistency level for the operation, or null to use the default consistency setting
     */
    public function getConsistency(): ?Consistency;

    /**
     * Get additional tuples to consider during the expansion.
     *
     * Returns a collection of temporary relationship tuples that are added to
     * the authorization data during evaluation. This allows you to test how
     * hypothetical or pending relationship changes would affect the authorization
     * graph without permanently modifying the store.
     *
     * @return TupleKeysInterface<TupleKeyInterface>|null Additional relationship tuples for evaluation, or null if none provided
     */
    public function getContextualTuples(): ?TupleKeysInterface;

    /**
     * Get the authorization model ID to use for the expansion.
     *
     * Specifies which version of the authorization model should be used when
     * expanding the relationship graph. Using a specific model ID ensures
     * consistent results even when the model is being updated. If not specified,
     * the latest model version will be used.
     *
     * @return string|null The authorization model ID for evaluation, or null to use the latest model version
     */
    public function getModel(): ?string;

    /**
     * Get the store ID containing the authorization data.
     *
     * Identifies which OpenFGA store contains the relationship tuples and
     * configuration to use for the expansion. All evaluation will be performed
     * within the context of this specific store.
     *
     * @return string The store ID containing the authorization data
     */
    public function getStore(): string;

    /**
     * Get the relationship tuple to expand.
     *
     * Specifies the starting point for the relationship expansion. This defines
     * the object and relation for which the authorization graph should be expanded.
     * The expansion will show all users and user sets that have the specified
     * relation to the specified object.
     *
     * @return TupleKeyInterface The relationship tuple specifying what to expand (object and relation)
     */
    public function getTupleKey(): TupleKeyInterface;
}
