<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use DateTimeImmutable;
use OpenFGA\Schemas\SchemaInterface;

/**
 * Interface for store creation response objects.
 *
 * This interface defines the contract for responses returned when creating new authorization
 * stores in OpenFGA. A store creation response contains the newly created store's metadata
 * including its unique identifier, name, and timestamps.
 *
 * Store creation is the foundational operation for establishing an authorization domain where
 * you can define relationship models, write authorization tuples, and perform permission checks.
 *
 * @see https://openfga.dev/api/service OpenFGA Create Store API Documentation
 */
interface CreateStoreResponseInterface extends ResponseInterface
{
    /**
     * Get the schema definition for this response.
     *
     * Returns the schema that defines the structure and validation rules for store creation
     * response data, ensuring consistent parsing and validation of API responses.
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
     * Get the unique identifier of the created store.
     *
     * Returns the system-generated unique identifier for the newly created store.
     * This ID is used in all subsequent API operations to reference this specific store.
     *
     * @return string The unique store identifier
     */
    public function getId(): string;

    /**
     * Get the human-readable name of the created store.
     *
     * Returns the descriptive name that was assigned to the store during creation.
     * This name is used for identification and administrative purposes.
     *
     * @return string The descriptive name of the store
     */
    public function getName(): string;

    /**
     * Get the timestamp when the store was last updated.
     *
     * Returns the timestamp of the most recent modification to the store's metadata.
     * For newly created stores, this will typically match the creation timestamp.
     *
     * @return DateTimeImmutable The last update timestamp of the store
     */
    public function getUpdatedAt(): DateTimeImmutable;
}
