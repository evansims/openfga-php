<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\TupleKeysInterface;

/**
 * Represents a single item in a batch check request.
 *
 * Each batch check item contains a tuple key to check, an optional context,
 * optional contextual tuples, and a correlation ID to map the result back
 * to this specific check.
 *
 * The correlation ID must be unique within the batch and follow the pattern:
 * alphanumeric characters or hyphens, maximum 36 characters.
 *
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/BatchCheck
 */
interface BatchCheckItemInterface extends ModelInterface
{
    /**
     * Get the context object for this check.
     *
     * This provides additional context data that can be used by conditions
     * in the authorization model during evaluation.
     *
     * @return ?object The context object or null
     */
    public function getContext(): ?object;

    /**
     * Get the contextual tuples for this check.
     *
     * These are additional tuples that are evaluated only for this specific
     * check and are not persisted in the store.
     *
     * @return ?TupleKeysInterface The contextual tuples or null
     */
    public function getContextualTuples(): ?TupleKeysInterface;

    /**
     * Get the correlation ID for this batch check item.
     *
     * This unique identifier maps the result back to this specific check.
     * Must be alphanumeric characters or hyphens, maximum 36 characters.
     *
     * @return string The correlation ID
     */
    public function getCorrelationId(): string;

    /**
     * Get the tuple key to be checked.
     *
     * This defines the user, relation, and object for the authorization check.
     *
     * @return TupleKeyInterface The tuple key for this check
     */
    public function getTupleKey(): TupleKeyInterface;
}
