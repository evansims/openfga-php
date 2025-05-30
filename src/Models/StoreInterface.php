<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeInterface;
use Override;

/**
 * Represents an OpenFGA store that contains authorization models and relationship tuples.
 *
 * A store is a logical container for all authorization data in OpenFGA, serving as the
 * fundamental organizational unit for authorization systems. Each store contains:
 * - Authorization models that define the permission structure for your application
 * - Relationship tuples that establish actual relationships between users and resources
 * - Configuration and metadata for authorization behavior
 *
 * Stores provide complete isolation between different authorization contexts, making them
 * ideal for multi-tenant applications where different customers, organizations, or
 * environments need separate authorization domains. Each store operates independently
 * with its own set of models, tuples, and access patterns.
 *
 * The store lifecycle includes creation, updates, and optional soft deletion, with
 * comprehensive timestamp tracking for audit and debugging purposes. Stores can be
 * managed through the OpenFGA management API and serve as the target for all
 * authorization queries and relationship operations.
 *
 * @see https://openfga.dev/docs/concepts#what-is-a-store OpenFGA Store Concept
 * @see https://openfga.dev/docs/getting-started/setup-openfga#creating-a-store Store Creation Guide
 */
interface StoreInterface extends ModelInterface
{
    /**
     * Get the timestamp when the store was created.
     *
     * The creation timestamp provides essential audit information and helps track
     * the lifecycle of authorization stores. This timestamp is set when the store
     * is first created through the OpenFGA API and remains immutable throughout
     * the store's lifetime.
     *
     * @return DateTimeInterface The creation timestamp in UTC timezone
     */
    public function getCreatedAt(): DateTimeInterface;

    /**
     * Get the timestamp when the store was deleted, if applicable.
     *
     * OpenFGA supports soft deletion of stores, allowing them to be marked as deleted
     * while preserving their data for audit and recovery purposes. When a store is
     * soft-deleted, this timestamp records when the deletion occurred. Active stores
     * will return null for this property.
     *
     * @return DateTimeInterface|null The deletion timestamp in UTC timezone, or null if the store is active
     */
    public function getDeletedAt(): ?DateTimeInterface;

    /**
     * Get the unique identifier of the store.
     *
     * The store ID is a globally unique identifier that serves as the primary key
     * for all operations within this authorization context. This ID is used in API
     * requests to target specific stores and ensure isolation between different
     * authorization domains in multi-tenant applications.
     *
     * @return string The store's unique identifier
     */
    public function getId(): string;

    /**
     * Get the human-readable name of the store.
     *
     * The store name provides a user-friendly identifier for administrative and
     * debugging purposes. Unlike the store ID, names can be changed and are intended
     * to be meaningful to developers and administrators managing authorization systems.
     * Names help identify stores in dashboards, logs, and management interfaces.
     *
     * @return string The store's display name
     */
    public function getName(): string;

    /**
     * Get the timestamp when the store was last updated.
     *
     * The update timestamp tracks when any changes were made to the store's metadata,
     * such as name changes or configuration updates. This timestamp is automatically
     * maintained by the OpenFGA service and provides important audit information
     * for tracking store modifications over time.
     *
     * @return DateTimeInterface The last update timestamp in UTC timezone
     */
    public function getUpdatedAt(): DateTimeInterface;

    /**
     * Serialize the store for JSON encoding.
     *
     * This method prepares the store data for API communication, converting all
     * properties into a format compatible with the OpenFGA API specification.
     * Timestamps are converted to RFC3339 format in UTC timezone, ensuring
     * consistent date handling across different systems and timezones.
     *
     * The resulting array contains all store properties with their API-compatible
     * names and values, ready for transmission to the OpenFGA service or storage
     * in JSON format.
     *
     * @return array<'created_at'|'deleted_at'|'id'|'name'|'updated_at', string> Store data formatted for JSON encoding with API-compatible field names
     */
    #[Override]
    public function jsonSerialize(): array;
}
