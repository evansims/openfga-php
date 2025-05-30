<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use DateTimeImmutable;

/**
 * Interface for listing historical changes to relationship tuples.
 *
 * This interface defines the contract for requests that query the change history
 * of relationship tuples within an OpenFGA store. It provides a chronological
 * audit trail of all tuple modifications, including writes and deletes, allowing
 * you to track how relationships have evolved over time.
 *
 * Tuple change history is essential for:
 * - **Auditing**: Track who made changes and when for compliance
 * - **Debugging**: Understand how authorization state reached its current condition
 * - **Synchronization**: Keep external systems in sync with authorization changes
 * - **Analytics**: Analyze access patterns and permission trends over time
 * - **Rollback**: Understand what changes need to be reversed
 *
 * The operation supports:
 * - Time-based filtering to focus on specific periods
 * - Object type filtering to track changes for specific resource types
 * - Pagination for handling large change histories efficiently
 * - Chronological ordering to understand the sequence of changes
 *
 * Each change entry includes the tuple that was modified, the type of operation
 * (write or delete), and the timestamp when the change occurred.
 *
 * @see DateTimeImmutable Time-based filtering for change history
 * @see https://openfga.dev/docs/api/service#Relationship%20Tuples/ReadChanges OpenFGA List Tuple Changes API Documentation
 */
interface ListTupleChangesRequestInterface extends RequestInterface
{
    /**
     * Get the continuation token for paginated results.
     *
     * Returns the pagination token from a previous list changes operation to
     * continue retrieving results from where the last request left off. This
     * enables efficient pagination through large change histories without
     * missing or duplicating entries.
     *
     * @return string|null The continuation token from a previous operation, or null for the first page
     */
    public function getContinuationToken(): ?string;

    /**
     * Get the maximum number of changes to return per page.
     *
     * Specifies the page size for paginated results. This controls how many
     * change entries are returned in a single response. Smaller page sizes
     * reduce memory usage and latency, while larger page sizes reduce the
     * number of API calls needed for extensive change histories.
     *
     * @return int|null The maximum number of changes to return per page, or null to use the default page size
     */
    public function getPageSize(): ?int;

    /**
     * Get the earliest time to include in the change history.
     *
     * Specifies the starting point for the time range of changes to retrieve.
     * Only changes that occurred at or after this time will be included in
     * the results. This allows you to focus on recent changes or specific
     * time periods of interest.
     *
     * @return DateTimeImmutable|null The earliest timestamp to include in results, or null to include all changes from the beginning
     */
    public function getStartTime(): ?DateTimeImmutable;

    /**
     * Get the store ID containing the tuple changes to list.
     *
     * Identifies which OpenFGA store contains the change history to query.
     * Each store maintains its own independent change log, ensuring complete
     * isolation of audit trails between different authorization domains.
     *
     * @return string The store ID containing the tuple change history to retrieve
     */
    public function getStore(): string;

    /**
     * Get the object type filter for changes.
     *
     * Specifies an optional filter to only include changes affecting tuples
     * of a specific object type. This helps narrow the results to changes
     * relevant to particular resource types, such as "document", "folder",
     * or "organization".
     *
     * @return string|null The object type to filter changes by, or null to include changes for all object types
     */
    public function getType(): ?string;
}
