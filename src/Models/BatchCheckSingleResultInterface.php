<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * Represents the result of a single check within a batch check response.
 *
 * Each result contains whether the check was allowed and any error information
 * if the check failed to complete successfully.
 *
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/BatchCheck
 */
interface BatchCheckSingleResultInterface extends ModelInterface
{
    /**
     * Get whether this check was allowed.
     *
     * Returns true if the user has the specified relationship with the object,
     * false if they don't, or null if the check encountered an error.
     *
     * @return ?bool True if allowed, false if not allowed, null if error occurred
     */
    public function getAllowed(): ?bool;

    /**
     * Get any error that occurred during this check.
     *
     * Returns error information if the check failed to complete successfully,
     * or null if the check completed without errors.
     *
     * @return ?object Error information or null if no error
     */
    public function getError(): ?object;
}
