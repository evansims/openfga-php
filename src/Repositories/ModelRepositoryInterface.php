<?php

declare(strict_types=1);

namespace OpenFGA\Repositories;

use OpenFGA\Models\Collections\{ConditionsInterface, TypeDefinitionsInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Results\{FailureInterface, SuccessInterface};

/**
 * Repository contract for authorization model operations.
 *
 * This interface defines the contract for managing authorization models within an OpenFGA store.
 * Authorization models define the permission structure for your application - the types of objects,
 * the relationships between them, and the rules that govern access. Models are immutable once
 * created; to update permissions, you create a new model version.
 *
 * All methods return Result objects following the Result pattern, allowing for consistent
 * error handling without exceptions.
 *
 * @see https://openfga.dev/docs/concepts#what-is-an-authorization-model Understanding authorization models
 */
interface ModelRepositoryInterface
{
    /**
     * Create a new authorization model in the store.
     *
     * Creates an immutable authorization model that defines your application's permission
     * structure. The model includes type definitions for objects and the relationships
     * between them, and optionally conditions for dynamic permissions.
     *
     * @param  TypeDefinitionsInterface          $typeDefinitions Object types and their relationship definitions
     * @param  SchemaVersion                     $schemaVersion   The schema version for the model (defaults to 1.1)
     * @param  ConditionsInterface|null          $conditions      Optional conditions for dynamic permissions
     * @return FailureInterface|SuccessInterface Success with the created AuthorizationModelInterface, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Authorization%20Models/CreateAuthorizationModel Creating models via API
     */
    public function create(
        TypeDefinitionsInterface $typeDefinitions,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
        ?ConditionsInterface $conditions = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Get a specific authorization model by ID.
     *
     * Retrieves the complete authorization model including all type definitions,
     * relationships, and conditions. Models are immutable, so the returned model
     * will never change once created.
     *
     * @param  string                            $modelId The unique identifier of the authorization model
     * @return FailureInterface|SuccessInterface Success with the AuthorizationModelInterface, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Authorization%20Models/GetAuthorizationModel Getting a specific model
     */
    public function get(string $modelId): FailureInterface | SuccessInterface;

    /**
     * List authorization models in the store.
     *
     * Returns a paginated list of authorization models, ordered by creation time
     * (newest first). Use pagination parameters to retrieve large lists efficiently.
     *
     * @param  int|null                          $pageSize          Maximum number of models to return (1-100)
     * @param  string|null                       $continuationToken Token from previous response for pagination
     * @return FailureInterface|SuccessInterface Success with AuthorizationModelsInterface collection, or Failure with error details
     *
     * @see https://openfga.dev/docs/api#/Authorization%20Models/ListAuthorizationModels Listing models with pagination
     */
    public function list(?int $pageSize = null, ?string $continuationToken = null): FailureInterface | SuccessInterface;
}
