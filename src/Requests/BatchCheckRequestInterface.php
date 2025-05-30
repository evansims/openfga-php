<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\BatchCheckItemsInterface;

/**
 * Request for performing multiple authorization checks in a single batch.
 *
 * This request allows checking multiple user-object relationships simultaneously
 * for better performance when multiple authorization decisions are needed.
 * Each check in the batch has a correlation ID to map results back to the
 * original requests.
 *
 * The batch check operation supports the same features as individual checks:
 * contextual tuples, custom contexts, and detailed error information.
 *
 * @see RequestInterface For the base request functionality
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/BatchCheck
 */
interface BatchCheckRequestInterface extends RequestInterface
{
    /**
     * Get the collection of checks to perform in this batch.
     *
     * Each item contains a tuple key to check and a correlation ID to map
     * the result back to this specific check.
     *
     * @return BatchCheckItemsInterface The batch check items
     */
    public function getChecks(): BatchCheckItemsInterface;
}
