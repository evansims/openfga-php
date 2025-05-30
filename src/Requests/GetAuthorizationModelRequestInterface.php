<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

/**
 * Interface for retrieving a specific authorization model.
 *
 * This interface defines the contract for requests that fetch a complete
 * authorization model from an OpenFGA store. Authorization models define
 * the relationship types, object types, and access control rules that
 * govern permission evaluation in your application.
 *
 * Retrieving authorization models is essential for:
 * - Inspecting current authorization schema and rules
 * - Building administrative interfaces for model management
 * - Implementing model comparison and diff functionality
 * - Backing up and versioning authorization configurations
 * - Understanding inheritance and relationship patterns
 * - Debugging authorization behavior and rule conflicts
 *
 * The retrieved model includes all type definitions, conditions, and
 * schema information needed to understand how permissions are evaluated.
 *
 * @see https://openfga.dev/docs/api/service#Authorization%20Models/ReadAuthorizationModel OpenFGA Get Authorization Model API Documentation
 * @see https://openfga.dev/docs/modeling OpenFGA Authorization Modeling Guide
 */
interface GetAuthorizationModelRequestInterface extends RequestInterface
{
    /**
     * Get the authorization model ID to retrieve.
     *
     * Specifies which version of the authorization model should be fetched
     * from the store. Each model has a unique identifier that allows you to
     * retrieve specific versions even as new models are created.
     *
     * @return string The unique identifier of the authorization model to retrieve
     */
    public function getModel(): string;

    /**
     * Get the store ID containing the authorization model.
     *
     * Identifies which OpenFGA store contains the authorization model to
     * retrieve. Each store can contain multiple model versions, and this
     * specifies which store context to search within.
     *
     * @return string The store ID containing the authorization model to retrieve
     */
    public function getStore(): string;
}
