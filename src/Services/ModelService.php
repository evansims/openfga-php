<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use OpenFGA\Models\Collections\AuthorizationModelsInterface;
use OpenFGA\Models\Collections\{ConditionsInterface, TypeDefinitionsInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Repositories\ModelRepositoryInterface;
use OpenFGA\Results\{Failure, FailureInterface, Success, SuccessInterface};
use OpenFGA\Translation\Translator;
use Override;
use ReflectionException;
use Throwable;

use function is_string;

/**
 * Service implementation for managing OpenFGA authorization models.
 *
 * Provides business-focused operations for working with authorization models,
 * including validation, convenience methods, and enhanced error handling.
 * This service abstracts the underlying repository implementation and adds
 * value through additional functionality.
 *
 * @see ModelServiceInterface Service interface
 * @see ModelRepositoryInterface Underlying repository
 */
final readonly class ModelService implements ModelServiceInterface
{
    /**
     * Create a new model service instance.
     *
     * @param ModelRepositoryInterface $modelRepository Repository for model data access
     * @param string                   $language        Language for error messages
     */
    public function __construct(
        private ModelRepositoryInterface $modelRepository,
        private string $language = 'en',
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function cloneModel(
        string $modelId,
    ): FailureInterface | SuccessInterface {
        // Get the source model
        $modelResult = $this->modelRepository->get($modelId);

        if ($modelResult instanceof FailureInterface) {
            return $modelResult;
        }

        /** @var AuthorizationModelInterface $sourceModel */
        $sourceModel = $modelResult->unwrap();

        // Create the model (assumes the repository is for the target store)
        // TODO: Support cross-store cloning with repository factory
        return $this->modelRepository->create(
            $sourceModel->getTypeDefinitions(),
            $sourceModel->getSchemaVersion(),
            $sourceModel->getConditions(),
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function createModel(
        TypeDefinitionsInterface $typeDefinitions,
        ?ConditionsInterface $conditions = null,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
    ): FailureInterface | SuccessInterface {
        // Validate type definitions first
        $validationResult = $this->validateModel($typeDefinitions, $schemaVersion);

        if ($validationResult instanceof FailureInterface) {
            return $validationResult;
        }

        // Delegate to repository for actual creation
        return $this->modelRepository->create($typeDefinitions, $schemaVersion, $conditions);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findModel(
        string $modelId,
    ): FailureInterface | SuccessInterface {
        // Delegate to repository for actual retrieval
        return $this->modelRepository->get($modelId);
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     * @throws Throwable                If result unwrapping fails
     *
     * @return FailureInterface|Success
     */
    #[Override]
    public function getLatestModel(
        StoreInterface | string $store,
    ): FailureInterface | SuccessInterface {
        // List models with page size 1 to get just the latest
        $result = $this->modelRepository->list(1);

        if ($result instanceof FailureInterface) {
            return $result;
        }

        /** @var AuthorizationModelsInterface $models */
        $models = $result->unwrap();

        if (0 === $models->count()) {
            return new Failure(ClientError::Validation->exception(context: [
                'message' => Translator::trans(
                    Messages::MODEL_NO_MODELS_IN_STORE,
                    ['store_id' => is_string($store) ? $store : $store->getId()],
                    $this->language,
                ),
            ]));
        }

        // Models are returned in reverse chronological order, so first is latest
        return new Success($models->first());
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listAllModels(
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): FailureInterface | SuccessInterface {
        // For now, delegate to repository with basic pagination
        // TODO: Implement enhanced pagination handling
        return $this->modelRepository->list(pageSize: $pageSize, continuationToken: $continuationToken);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function validateModel(
        TypeDefinitionsInterface $typeDefinitions,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
    ): FailureInterface | SuccessInterface {
        try {
            // Basic validation
            if (0 === $typeDefinitions->count()) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::MODEL_TYPE_DEFINITIONS_EMPTY, [], $this->language), ]);
            }

            // Check for duplicate type names
            $typeNames = [];

            foreach ($typeDefinitions as $typeDefinition) {
                $typeName = $typeDefinition->getType();

                if (isset($typeNames[$typeName])) {
                    throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::MODEL_DUPLICATE_TYPE, ['type' => $typeName], $this->language), ]);
                }
                $typeNames[$typeName] = true;
            }

            // Additional validation could be added here
            // For now, we'll rely on the server validation when creating

            return new Success(true);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }
}
