<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\BatchCheckSingleResultInterface;

/**
 * Response containing the results of a batch authorization check.
 *
 * This response contains a map of correlation IDs to check results, allowing
 * you to match each result back to the original check request using the
 * correlation ID that was provided in the batch request.
 *
 * @see ResponseInterface For the base response functionality
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/BatchCheck
 */
interface BatchCheckResponseInterface extends ResponseInterface
{
    /**
     * Get the results map from correlation IDs to check results.
     *
     * Each key in the map is a correlation ID from the original request,
     * and each value is the result of that specific check.
     *
     * @return array<string, BatchCheckSingleResultInterface> Map of correlation ID to check result
     */
    public function getResult(): array;

    /**
     * Get the result for a specific correlation ID.
     *
     * Returns the check result for the given correlation ID, or null if
     * no result exists for that ID.
     *
     * @param  string                           $correlationId The correlation ID to look up
     * @return ?BatchCheckSingleResultInterface The check result or null if not found
     */
    public function getResultForCorrelationId(string $correlationId): ?BatchCheckSingleResultInterface;
}
