<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;

/**
 * Request interface for streaming objects that a user has a specific relationship with.
 *
 * This request finds all objects of a given type where the specified user has
 * the requested relationship, but returns them as a stream for processing large
 * result sets efficiently. It's useful for building resource lists, dashboards,
 * or any interface that shows what a user can access when dealing with large datasets.
 *
 * @see https://openfga.dev/api/service#/Relationship%20Queries/StreamedListObjects Streamed list objects API endpoint
 */
interface StreamedListObjectsRequestInterface extends RequestInterface
{
    /**
     * Get the consistency requirement for this request.
     *
     * @return Consistency|null The consistency requirement, or null if not specified
     */
    public function getConsistency(): ?Consistency;

    /**
     * Get the context object for this request.
     *
     * @return object|null The context object, or null if not specified
     */
    public function getContext(): ?object;

    /**
     * Get the contextual tuples for this request.
     *
     * @return TupleKeysInterface|null The contextual tuples collection, or null if not specified
     */
    public function getContextualTuples(): ?TupleKeysInterface;

    /**
     * Get the authorization model ID for this request.
     *
     * @return string|null The authorization model ID, or null if not specified
     */
    public function getModel(): ?string;

    /**
     * Get the relation name for this request.
     *
     * @return string The relation name to check
     */
    public function getRelation(): string;

    /**
     * Get the store ID for this request.
     *
     * @return string The store ID
     */
    public function getStore(): string;

    /**
     * Get the object type for this request.
     *
     * @return string The object type to list
     */
    public function getType(): string;

    /**
     * Get the user identifier for this request.
     *
     * @return string The user identifier
     */
    public function getUser(): string;
}
