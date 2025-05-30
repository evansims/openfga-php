<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

/**
 * Interface for creating a new OpenFGA store.
 *
 * This interface defines the contract for requests that create new authorization
 * stores in OpenFGA. A store is an isolated container for authorization data,
 * including relationship tuples, authorization models, and configuration.
 *
 * Each store provides:
 * - Complete isolation of authorization data from other stores
 * - Independent versioning of authorization models
 * - Separate configuration and access controls
 * - Dedicated API endpoints for all operations
 *
 * Creating a store establishes a new authorization domain where you can define
 * relationship models, write authorization tuples, and perform permission checks.
 * The store name serves as a human-readable identifier for administrative purposes.
 *
 * @see https://openfga.dev/docs/api/service#Stores/CreateStore OpenFGA Create Store API Documentation
 * @see https://openfga.dev/docs/concepts#what-is-a-store OpenFGA Store Concepts
 */
interface CreateStoreRequestInterface extends RequestInterface
{
    /**
     * Get the name for the new store.
     *
     * Returns the human-readable name that will be assigned to the new store.
     * This name is used for identification and administrative purposes and
     * should be descriptive enough to distinguish the store from others in
     * your organization.
     *
     * The store name:
     * - Must be a non-empty string
     * - Should be descriptive and meaningful for administrative purposes
     * - Is used for display in management interfaces and logging
     * - Does not need to be globally unique (the store ID serves that purpose)
     *
     * @return string The descriptive name for the new authorization store
     */
    public function getName(): string;
}
