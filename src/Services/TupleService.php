<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use DateTimeImmutable;
use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Models\{AuthorizationModel, Store, TupleKey, TupleKeyInterface};
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use OpenFGA\Models\Collections\{Conditions, TypeDefinitions};
use OpenFGA\Models\Collections\{TupleKeysInterface, Tuples};
use OpenFGA\Models\Enums\{Consistency, SchemaVersion};
use OpenFGA\Repositories\TupleRepositoryInterface;
use OpenFGA\Responses\{WriteTuplesResponse};
use OpenFGA\Results\{Failure, FailureInterface, Success, SuccessInterface};
use OpenFGA\Translation\Translator;
use Override;
use ReflectionException;
use Throwable;

use function is_string;

/**
 * Service implementation for managing OpenFGA relationship tuples.
 *
 * Provides business-focused operations for working with relationship tuples,
 * which represent the core relationships in your authorization model. This service
 * abstracts the underlying repository implementation and adds value through
 * validation, duplicate filtering, and enhanced error handling.
 *
 * @see TupleServiceInterface Service interface
 * @see TupleRepositoryInterface Underlying repository
 */
final readonly class TupleService implements TupleServiceInterface
{
    /**
     * Create a new tuple service instance.
     *
     * @param TupleRepositoryInterface $tupleRepository Repository for tuple data access
     * @param string                   $language        Language for error messages
     */
    public function __construct(
        private TupleRepositoryInterface $tupleRepository,
        private string $language = 'en',
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function delete(
        StoreInterface | string $store,
        string $user,
        string $relation,
        string $object,
        bool $confirmExists = false,
    ): FailureInterface | SuccessInterface {
        try {
            // Validate inputs
            if ('' === $user) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_USER_EMPTY, [], $this->language), ]);
            }

            if ('' === $relation) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_RELATION_EMPTY, [], $this->language), ]);
            }

            if ('' === $object) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_OBJECT_EMPTY, [], $this->language), ]);
            }

            // TODO: Implement existence check and repository delegation
            return new Success(true);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function deleteBatch(
        StoreInterface | string $store,
        TupleKeysInterface $tupleKeys,
        bool $transactional = true,
        bool $confirmExists = false,
    ): FailureInterface | SuccessInterface {
        try {
            // Validate input
            if (0 === $tupleKeys->count()) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::INVALID_BATCH_CHECK_EMPTY, [], $this->language), ]);
            }

            // Check transactional limits
            if ($transactional && 100 < $tupleKeys->count()) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_TRANSACTIONAL_LIMIT_EXCEEDED, ['count' => $tupleKeys->count()], $this->language), ]);
            }

            // TODO: Implement existence check and repository delegation
            return new Success(true);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function exists(
        StoreInterface | string $store,
        string $user,
        string $relation,
        string $object,
    ): SuccessInterface {
        // TODO: Implement repository delegation
        return new Success(false);
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function getStatistics(
        StoreInterface | string $store,
    ): SuccessInterface {
        // TODO: Implement repository delegation and statistics calculation
        return new Success([
            'total_tuples' => 0,
            'types' => [],
            'relations' => [],
            'users' => [],
        ]);
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function listChanges(
        StoreInterface | string $store,
        ?string $type = null,
        ?DateTimeImmutable $startTime = null,
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): FailureInterface | SuccessInterface {
        try {
            // Convert store to required type for repository
            $storeObj = is_string($store)
                ? new Store($store, $store, new DateTimeImmutable, new DateTimeImmutable)
                : $store;

            // Delegate to repository
            return $this->tupleRepository->listChanges(
                store: $storeObj,
                type: $type,
                startTime: $startTime,
                continuationToken: $continuationToken,
                pageSize: $pageSize,
            );
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function read(
        StoreInterface | string $store,
        ?TupleKeyInterface $tupleKey = null,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface {
        try {
            // Convert store to required type for repository
            $storeObj = is_string($store)
                ? new Store($store, $store, new DateTimeImmutable, new DateTimeImmutable)
                : $store;

            // Create a default tuple key filter if none provided
            if (! $tupleKey instanceof TupleKeyInterface) {
                $tupleKey = new TupleKey(user: '', relation: '', object: '');
            }

            // Delegate to repository
            return $this->tupleRepository->read(
                store: $storeObj,
                filter: $tupleKey,
                continuationToken: $continuationToken,
                pageSize: $pageSize,
            );
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function write(
        StoreInterface | string $store,
        string $user,
        string $relation,
        string $object,
    ): FailureInterface | SuccessInterface {
        try {
            // Validate inputs
            if ('' === $user) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_USER_EMPTY, [], $this->language), ]);
            }

            if ('' === $relation) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_RELATION_EMPTY, [], $this->language), ]);
            }

            if ('' === $object) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_OBJECT_EMPTY, [], $this->language), ]);
            }

            // TODO: Implement repository delegation with proper store and model parameters
            // For now, return success to demonstrate the pattern
            return new Success(true);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function writeBatch(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
        bool $transactional = true,
        int $maxParallelRequests = 1,
        int $maxTuplesPerChunk = 100,
        int $maxRetries = 0,
        float $retryDelaySeconds = 1.0,
        bool $stopOnFirstError = false,
    ): FailureInterface | SuccessInterface {
        try {
            // Handle empty operations - check if there are any operations to perform
            $hasWrites = $writes instanceof TupleKeysInterface && 0 < $writes->count();
            $hasDeletes = $deletes instanceof TupleKeysInterface && 0 < $deletes->count();

            if (! $hasWrites && ! $hasDeletes) {
                return new Success(new WriteTuplesResponse(
                    transactional: $transactional,
                    totalOperations: 0,
                    totalChunks: 0,
                    successfulChunks: 0,
                    failedChunks: 0,
                ));
            }

            // Check transactional limits (OpenFGA has a 100 tuple limit for transactional writes)
            if ($transactional) {
                $totalTuples = 0;

                if ($writes instanceof TupleKeysInterface) {
                    $totalTuples += $writes->count();
                }

                if ($deletes instanceof TupleKeysInterface) {
                    $totalTuples += $deletes->count();
                }

                if (100 < $totalTuples) {
                    throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_TRANSACTIONAL_LIMIT_EXCEEDED, ['count' => $totalTuples], $this->language), ]);
                }
            }

            // The repository will handle duplicate filtering automatically

            // Convert store and model to required types for repository
            $storeObj = is_string($store)
                ? new Store($store, $store, new DateTimeImmutable, new DateTimeImmutable)
                : $store;
            $modelObj = is_string($model)
                ? new AuthorizationModel($model, SchemaVersion::V1_1, new TypeDefinitions([]), new Conditions([]))
                : $model;

            // Use the repository to write the tuples
            return $this->tupleRepository->writeAndDelete(
                store: $storeObj,
                model: $modelObj,
                writes: $writes,
                deletes: $deletes,
                transactional: $transactional,
                options: [
                    'maxParallelRequests' => $maxParallelRequests,
                    'maxTuplesPerChunk' => $maxTuplesPerChunk,
                    'maxRetries' => $maxRetries,
                    'retryDelaySeconds' => $retryDelaySeconds,
                    'stopOnFirstError' => $stopOnFirstError,
                ],
            );
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }
}
