<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;

/**
 * Interface for authorization check request specifications.
 *
 * This interface defines the contract for creating authorization check requests
 * that determine whether a user has a specific relationship with an object.
 * It's the core interface for implementing permission verification in applications.
 *
 * @see CheckRequest For the concrete implementation
 */
interface CheckRequestInterface extends RequestInterface
{
    /**
     * Get the authorization model ID to use for the check.
     *
     * This specifies which version of the authorization model should be used when evaluating
     * the permission check. Using a specific model ID ensures consistent results.
     *
     * @return string The authorization model ID for permission evaluation
     */
    public function getAuthorizationModel(): string;

    /**
     * Get the consistency level for the check operation.
     *
     * This determines the read consistency requirement for the check operation, allowing you
     * to balance between read performance and data consistency based on your application's needs.
     *
     * @return Consistency|null The consistency level, or null to use the default consistency setting
     */
    public function getConsistency(): ?Consistency;

    /**
     * Get additional context data for conditional evaluation.
     *
     * This provides contextual information that can be used in conditional expressions within
     * the authorization model, enabling dynamic permission evaluation based on runtime data.
     *
     * @return object|null The context object containing additional data for evaluation, or null if no context is provided
     */
    public function getContext(): ?object;

    /**
     * Get additional tuples to consider during the check.
     *
     * These contextual tuples are temporarily added to the authorization data during evaluation,
     * allowing you to test permission scenarios with hypothetical or pending relationship changes.
     *
     * @return TupleKeysInterface<TupleKeyInterface>|null Additional relationship tuples for evaluation, or null if none provided
     */
    public function getContextualTuples(): ?TupleKeysInterface;

    /**
     * Get the store ID containing the authorization data.
     *
     * This identifies which OpenFGA store contains the relationship tuples and configuration
     * to use for the permission check.
     *
     * @return string The store ID containing the authorization data
     */
    public function getStore(): string;

    /**
     * Get whether to include evaluation trace in the response.
     *
     * When enabled, the response will include detailed information about how the permission
     * decision was reached, which is useful for debugging authorization logic.
     *
     * @return bool|null Whether to include trace information, or null to use the default setting
     */
    public function getTrace(): ?bool;

    /**
     * Get the relationship tuple to check for permission.
     *
     * This defines the specific relationship (user, object, relation) to evaluate for authorization.
     * For example, checking if "user:alice" has "can_view" permission on "document:readme".
     *
     * @return TupleKeyInterface The relationship tuple specifying what permission to check
     */
    public function getTupleKey(): TupleKeyInterface;
}
