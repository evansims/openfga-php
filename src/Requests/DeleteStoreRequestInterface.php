<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

/**
 * Interface for deleting an OpenFGA store.
 *
 * This interface defines the contract for requests that permanently remove
 * an authorization store from OpenFGA. Deleting a store is an irreversible
 * operation that removes all associated data including relationship tuples,
 * authorization models, assertions, and configuration.
 *
 * Store deletion is typically used for:
 * - Cleaning up test or development environments
 * - Removing stores for discontinued projects or applications
 * - Implementing data retention policies and compliance requirements
 * - Freeing up resources and reducing storage costs
 *
 * **Warning:** This operation is permanent and cannot be undone. All authorization
 * data within the store will be lost, including relationship tuples, authorization
 * models, and any custom configurations. Ensure you have proper backups and
 * authorization before performing this operation.
 *
 * @see https://openfga.dev/docs/api/service#Stores/DeleteStore OpenFGA Delete Store API Documentation
 * @see https://openfga.dev/docs/concepts#what-is-a-store OpenFGA Store Concepts
 */
interface DeleteStoreRequestInterface extends RequestInterface
{
    /**
     * Get the ID of the store to delete.
     *
     * Returns the unique identifier of the store that will be permanently
     * removed from OpenFGA. This operation will delete all data associated
     * with the store, including relationship tuples, authorization models,
     * and configuration settings.
     *
     * **Important:** This is a destructive operation that cannot be reversed.
     * Ensure you have the correct store ID and proper authorization before
     * proceeding with the deletion.
     *
     * @return string The unique identifier of the store to permanently delete
     */
    public function getStore(): string;
}
