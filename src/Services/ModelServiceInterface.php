<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use OpenFGA\Models\Collections\{ConditionsInterface, TypeDefinitionsInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Results\{FailureInterface, SuccessInterface};
use Throwable;

/**
 * Service interface for managing OpenFGA authorization models.
 *
 * This service provides business-focused operations for working with authorization
 * models, abstracting away the underlying repository implementation details and
 * providing enhanced functionality like validation, cloning, and convenience methods.
 *
 * Authorization models define the permission structure for your application,
 * including object types, relationships, and computation rules. Models are
 * immutable once created, ensuring consistent authorization behavior.
 *
 * ## Core Operations
 *
 * The service supports model management with enhanced functionality:
 * - Create models with comprehensive validation
 * - Retrieve models with improved error handling
 * - Clone models between stores for multi-tenant scenarios
 * - Find the latest model version automatically
 *
 * ## Usage Example
 *
 * ```php
 * $modelService = new ModelService($modelRepository);
 *
 * // Create a new model with validation
 * $result = $modelService->createModel(
 *     $store,
 *     $typeDefinitions,
 *     $conditions
 * );
 *
 * // Get the latest model for a store
 * $latest = $modelService->getLatestModel($store)->unwrap();
 *
 * // Clone a model to another store
 * $cloned = $modelService->cloneModel(
 *     $sourceStore,
 *     $modelId,
 *     $targetStore
 * )->unwrap();
 * ```
 *
 * @see ModelRepositoryInterface Underlying repository for data access
 * @see AuthorizationModelInterface Model representation
 */
interface ModelServiceInterface
{
    /**
     * Clone an authorization model to another store.
     *
     * Copies a model from one store to another, useful for multi-tenant scenarios
     * where you want to replicate a permission structure. The cloned model gets
     * a new ID in the target store.
     *
     * @param StoreInterface|string $fromStore The source store containing the model
     * @param string                $modelId   The ID of the model to clone
     * @param StoreInterface|string $toStore   The target store where the model will be created
     *
     * @throws Throwable If result unwrapping fails
     *
     * @return FailureInterface|SuccessInterface Success with the cloned model, or Failure with error details
     */
    public function cloneModel(
        StoreInterface | string $fromStore,
        string $modelId,
        StoreInterface | string $toStore,
    ): FailureInterface | SuccessInterface;

    /**
     * Create a new authorization model with validation.
     *
     * Creates an immutable authorization model from the provided type definitions
     * and optional conditions. The model is validated before creation to ensure
     * it conforms to OpenFGA's schema requirements.
     *
     * @param  StoreInterface|string             $store           The store where the model will be created
     * @param  TypeDefinitionsInterface          $typeDefinitions The type definitions for the model
     * @param  ConditionsInterface|null          $conditions      Optional conditions for attribute-based access control
     * @param  SchemaVersion                     $schemaVersion   The OpenFGA schema version to use
     * @return FailureInterface|SuccessInterface Success with the created model, or Failure with validation/creation errors
     */
    public function createModel(
        StoreInterface | string $store,
        TypeDefinitionsInterface $typeDefinitions,
        ?ConditionsInterface $conditions = null,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
    ): FailureInterface | SuccessInterface;

    /**
     * Find a specific authorization model by ID.
     *
     * Retrieves a model with enhanced error handling, providing clear messages
     * when models are not found or other errors occur.
     *
     * @param  StoreInterface|string             $store   The store containing the model
     * @param  string                            $modelId The unique identifier of the model
     * @return FailureInterface|SuccessInterface Success with the model, or Failure with detailed error information
     */
    public function findModel(
        StoreInterface | string $store,
        string $modelId,
    ): FailureInterface | SuccessInterface;

    /**
     * Get the most recent authorization model for a store.
     *
     * Retrieves the latest model version, which is typically the active model
     * being used for authorization decisions. This is a convenience method that
     * avoids needing to list all models and manually find the newest one.
     *
     * @param  StoreInterface|string             $store The store to get the latest model from
     * @return FailureInterface|SuccessInterface Success with the latest model, or Failure if no models exist
     */
    public function getLatestModel(
        StoreInterface | string $store,
    ): FailureInterface | SuccessInterface;

    /**
     * List all authorization models for a store.
     *
     * Retrieves all models with automatic pagination handling. This method
     * aggregates results across multiple pages up to the specified limit.
     *
     * @param  StoreInterface|string             $store    The store to list models from
     * @param  int|null                          $maxItems Maximum number of models to retrieve (null for all)
     * @return FailureInterface|SuccessInterface Success with the models collection, or Failure with error details
     */
    public function listAllModels(
        StoreInterface | string $store,
        ?int $maxItems = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Validate type definitions before creating a model.
     *
     * Performs validation on type definitions to catch errors before attempting
     * to create a model. This is useful for providing immediate feedback in
     * user interfaces or validation pipelines.
     *
     * @param  TypeDefinitionsInterface          $typeDefinitions The type definitions to validate
     * @param  SchemaVersion                     $schemaVersion   The schema version to validate against
     * @return FailureInterface|SuccessInterface Success if valid, or Failure with validation errors
     */
    public function validateModel(
        TypeDefinitionsInterface $typeDefinitions,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
    ): FailureInterface | SuccessInterface;
}
