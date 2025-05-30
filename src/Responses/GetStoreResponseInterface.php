<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use DateTimeImmutable;
use OpenFGA\Models\StoreInterface;
use OpenFGA\Schema\SchemaInterface;

/**
 * Interface for store retrieval response objects.
 *
 * This interface defines the contract for responses returned when retrieving store
 * information from OpenFGA. A store retrieval response contains comprehensive metadata
 * about the store including its identifier, name, timestamps, and full store object.
 *
 * Store retrieval is useful for administrative operations, auditing, and displaying
 * store information in management interfaces.
 *
 * @see StoreInterface The complete store object structure
 * @see https://openfga.dev/api/service OpenFGA Get Store API Documentation
 */
interface GetStoreResponseInterface extends ResponseInterface
{
    /**
     * Get the schema definition for this response.
     *
     * Returns the schema that defines the structure and validation rules for store
     * retrieval response data, ensuring consistent parsing and validation of API responses.
     *
     * @return SchemaInterface The schema definition for response validation
     */
    public static function schema(): SchemaInterface;

    /**
     * Get the timestamp when the store was created.
     *
     * Returns the exact moment when the store was successfully created in the OpenFGA system.
     * This timestamp is immutable and set by the server upon store creation.
     *
     * @return DateTimeImmutable The creation timestamp of the store
     */
    public function getCreatedAt(): DateTimeImmutable;

    /**
     * Get the timestamp when the store was deleted, if applicable.
     *
     * Returns the deletion timestamp for soft-deleted stores, or null if the store
     * is active. This is used for stores that have been marked for deletion but
     * may still be accessible for a grace period.
     *
     * @return DateTimeImmutable|null The deletion timestamp, or null if the store is not deleted
     */
    public function getDeletedAt(): ?DateTimeImmutable;

    /**
     * Get the unique identifier of the store.
     *
     * Returns the system-generated unique identifier for the store. This ID is used
     * in all API operations to reference this specific store.
     *
     * @return string The unique store identifier
     */
    public function getId(): string;

    /**
     * Get the human-readable name of the store.
     *
     * Returns the descriptive name that was assigned to the store during creation
     * or last update. This name is used for identification and administrative purposes.
     *
     * @return string The descriptive name of the store
     */
    public function getName(): string;

    /**
     * Get the complete store object.
     *
     * Returns the full store object containing all store metadata and configuration.
     * This provides access to the complete store data structure including any
     * additional properties beyond the individual accessor methods.
     *
     * @return StoreInterface The complete store object
     */
    public function getStore(): StoreInterface;

    /**
     * Get the timestamp when the store was last updated.
     *
     * Returns the timestamp of the most recent modification to the store's metadata
     * or configuration. This is updated whenever store properties are changed.
     *
     * @return DateTimeImmutable The last update timestamp of the store
     */
    public function getUpdatedAt(): DateTimeImmutable;
}
