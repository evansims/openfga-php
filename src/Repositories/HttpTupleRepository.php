<?php

declare(strict_types=1);

namespace OpenFGA\Repositories;

use DateTimeImmutable;
use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Requests\{ListTupleChangesRequest, ReadTuplesRequest, WriteTuplesRequest};
use OpenFGA\Responses\{ListTupleChangesResponse, ReadTuplesResponse, WriteTuplesResponse};
use OpenFGA\Results\{Failure, FailureInterface, Success, SuccessInterface};
use OpenFGA\Schemas\SchemaValidatorInterface;
use OpenFGA\Services\{HttpServiceInterface, TupleFilterServiceInterface};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Throwable;

use function count;
use function max;
use function min;
use function usleep;

/**
 * HTTP implementation of the tuple repository.
 *
 * This repository handles tuple operations via HTTP requests to the OpenFGA API.
 * It converts domain objects to API requests, sends them via the HTTP service,
 * and transforms responses back to domain objects. Supports both transactional
 * and non-transactional tuple operations with proper error handling.
 *
 * @see TupleRepositoryInterface For the domain contract
 * @see https://openfga.dev/docs/api#/Relationship%20Tuples Tuple operations documentation
 */
final readonly class HttpTupleRepository implements TupleRepositoryInterface
{
    private const int MAX_PAGE_SIZE = 100;

    private const int MAX_TRANSACTIONAL_OPERATIONS = 100;

    /**
     * @param HttpServiceInterface        $httpService        Service for making HTTP requests
     * @param TupleFilterServiceInterface $tupleFilterService Service for filtering duplicate tuples
     * @param SchemaValidatorInterface    $validator          Validator for API responses
     */
    public function __construct(
        private HttpServiceInterface $httpService,
        private TupleFilterServiceInterface $tupleFilterService,
        private SchemaValidatorInterface $validator,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function delete(
        StoreInterface $store,
        AuthorizationModelInterface $model,
        TupleKeysInterface $tuples,
        bool $transactional = true,
        array $options = [],
    ): FailureInterface | SuccessInterface {
        /** @var array{maxParallelRequests?: int, maxTuplesPerChunk?: int, maxRetries?: int, retryDelaySeconds?: float, stopOnFirstError?: bool} $options */
        try {
            // Filter duplicates before deletion
            [, $filteredDeletes] = $this->tupleFilterService->filterDuplicates(null, $tuples);

            // Check if we have any tuples to delete
            if (! $filteredDeletes instanceof TupleKeysInterface || 0 === $filteredDeletes->count()) {
                return new Success(new WriteTuplesResponse(
                    transactional: $transactional,
                    totalOperations: 0,
                    totalChunks: 0,
                    successfulChunks: 0,
                    failedChunks: 0,
                ));
            }

            // Validate transactional limit
            if ($transactional && self::MAX_TRANSACTIONAL_OPERATIONS < $filteredDeletes->count()) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_TRANSACTIONAL_LIMIT_EXCEEDED, ['count' => $filteredDeletes->count()], )], );
            }

            // Create write request with deletes only
            $request = new WriteTuplesRequest(
                store: $store->getId(),
                model: $model->getId(),
                writes: null,
                deletes: $filteredDeletes,
                transactional: $transactional,
                maxParallelRequests: $options['maxParallelRequests'] ?? 1,
                maxTuplesPerChunk: $options['maxTuplesPerChunk'] ?? 100,
                maxRetries: $options['maxRetries'] ?? 0,
                retryDelaySeconds: $options['retryDelaySeconds'] ?? 1.0,
                stopOnFirstError: $options['stopOnFirstError'] ?? false,
            );

            // Process the request
            return $this->processWriteRequest($request);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listChanges(
        StoreInterface $store,
        ?string $type = null,
        ?DateTimeImmutable $startTime = null,
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): FailureInterface | SuccessInterface {
        try {
            // Normalize page size
            $pageSize = null !== $pageSize ? max(1, min($pageSize, self::MAX_PAGE_SIZE)) : null;

            $request = new ListTupleChangesRequest(
                store: $store->getId(),
                continuationToken: $continuationToken,
                pageSize: $pageSize,
                type: $type,
                startTime: $startTime,
            );

            $response = $this->httpService->send($request);

            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof RequestInterface) {
                throw ClientError::Network->exception(context: ['message' => 'Failed to capture HTTP request'], );
            }

            return new Success(ListTupleChangesResponse::fromResponse(
                $response,
                $lastRequest,
                $this->validator,
            ));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function read(
        StoreInterface $store,
        TupleKeyInterface $filter,
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): FailureInterface | SuccessInterface {
        try {
            // Normalize page size
            $pageSize = null !== $pageSize ? max(1, min($pageSize, self::MAX_PAGE_SIZE)) : null;

            $request = new ReadTuplesRequest(
                tupleKey: $filter,
                store: $store->getId(),
                continuationToken: $continuationToken,
                pageSize: $pageSize,
                consistency: null, // Can be added to options if needed
            );

            $response = $this->httpService->send($request);

            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof RequestInterface) {
                throw ClientError::Network->exception(context: ['message' => 'Failed to capture HTTP request'], );
            }

            return new Success(ReadTuplesResponse::fromResponse(
                $response,
                $lastRequest,
                $this->validator,
            ));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function write(
        StoreInterface $store,
        AuthorizationModelInterface $model,
        TupleKeysInterface $tuples,
        bool $transactional = true,
        array $options = [],
    ): FailureInterface | SuccessInterface {
        /** @var array{maxParallelRequests?: int, maxTuplesPerChunk?: int, maxRetries?: int, retryDelaySeconds?: float, stopOnFirstError?: bool} $options */
        try {
            // Filter duplicates before writing
            [$filteredWrites, ] = $this->tupleFilterService->filterDuplicates($tuples, null);

            // Check if we have any tuples to write
            if (! $filteredWrites instanceof TupleKeysInterface || 0 === $filteredWrites->count()) {
                return new Success(new WriteTuplesResponse(
                    transactional: $transactional,
                    totalOperations: 0,
                    totalChunks: 0,
                    successfulChunks: 0,
                    failedChunks: 0,
                ));
            }

            // Validate transactional limit
            if ($transactional && self::MAX_TRANSACTIONAL_OPERATIONS < $filteredWrites->count()) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_TRANSACTIONAL_LIMIT_EXCEEDED, ['count' => $filteredWrites->count()], )], );
            }

            // Create write request with writes only
            $request = new WriteTuplesRequest(
                store: $store->getId(),
                model: $model->getId(),
                writes: $filteredWrites,
                deletes: null,
                transactional: $transactional,
                maxParallelRequests: $options['maxParallelRequests'] ?? 1,
                maxTuplesPerChunk: $options['maxTuplesPerChunk'] ?? 100,
                maxRetries: $options['maxRetries'] ?? 0,
                retryDelaySeconds: $options['retryDelaySeconds'] ?? 1.0,
                stopOnFirstError: $options['stopOnFirstError'] ?? false,
            );

            // Process the request
            return $this->processWriteRequest($request);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * Write and delete tuples in a single operation.
     *
     * @param  StoreInterface                    $store         The store to operate on
     * @param  AuthorizationModelInterface       $model         The authorization model to validate against
     * @param  TupleKeysInterface|null           $writes        Tuples to write (optional)
     * @param  TupleKeysInterface|null           $deletes       Tuples to delete (optional)
     * @param  bool                              $transactional Whether to use transactional mode
     * @param  array<string, mixed>              $options       Additional options for non-transactional mode
     * @return FailureInterface|SuccessInterface Result of the operation
     */
    #[Override]
    public function writeAndDelete(
        StoreInterface $store,
        AuthorizationModelInterface $model,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
        bool $transactional = true,
        array $options = [],
    ): FailureInterface | SuccessInterface {
        /** @var array{maxParallelRequests?: int, maxTuplesPerChunk?: int, maxRetries?: int, retryDelaySeconds?: float, stopOnFirstError?: bool} $options */
        try {
            // Filter duplicates
            [$filteredWrites, $filteredDeletes] = $this->tupleFilterService->filterDuplicates($writes, $deletes);

            // Check if we have any operations
            $writeCount = $filteredWrites?->count() ?? 0;
            $deleteCount = $filteredDeletes?->count() ?? 0;
            $totalOperations = $writeCount + $deleteCount;

            if (0 === $totalOperations) {
                return new Success(new WriteTuplesResponse(
                    transactional: $transactional,
                    totalOperations: 0,
                    totalChunks: 0,
                    successfulChunks: 0,
                    failedChunks: 0,
                ));
            }

            // Validate transactional limit
            if ($transactional && self::MAX_TRANSACTIONAL_OPERATIONS < $totalOperations) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_TRANSACTIONAL_LIMIT_EXCEEDED, ['count' => $totalOperations], ), ]);
            }

            // Create write request with both writes and deletes
            $request = new WriteTuplesRequest(
                store: $store->getId(),
                model: $model->getId(),
                writes: $filteredWrites,
                deletes: $filteredDeletes,
                transactional: $transactional,
                maxParallelRequests: $options['maxParallelRequests'] ?? 1,
                maxTuplesPerChunk: $options['maxTuplesPerChunk'] ?? 100,
                maxRetries: $options['maxRetries'] ?? 0,
                retryDelaySeconds: $options['retryDelaySeconds'] ?? 1.0,
                stopOnFirstError: $options['stopOnFirstError'] ?? false,
            );

            // Process the request
            return $this->processWriteRequest($request);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * Process a single chunk with retry logic.
     *
     * @param WriteTuplesRequest $chunk           The chunk to process
     * @param WriteTuplesRequest $originalRequest The original request for retry settings
     *
     * @throws RuntimeException If retry logic fails unexpectedly
     *
     * @return FailureInterface|SuccessInterface Result of the chunk processing
     */
    private function processChunkWithRetries(
        WriteTuplesRequest $chunk,
        WriteTuplesRequest $originalRequest,
    ): FailureInterface | SuccessInterface {
        $attempt = 0;
        $maxRetries = $originalRequest->getMaxRetries();
        $retryDelaySeconds = $originalRequest->getRetryDelaySeconds();

        while ($attempt <= $maxRetries) {
            try {
                $response = $this->httpService->send($chunk);

                $lastRequest = $this->httpService->getLastRequest();

                if (! $lastRequest instanceof RequestInterface) {
                    // For successful responses, we can create a basic WriteTuplesResponse
                    // even if we couldn't capture the request (e.g., in test scenarios)
                    if (200 <= $response->getStatusCode() && 300 > $response->getStatusCode()) {
                        return new Success(new WriteTuplesResponse(
                            transactional: $originalRequest->isTransactional(),
                            totalOperations: $chunk->getTotalOperations(),
                            totalChunks: 1,
                            successfulChunks: 1,
                            failedChunks: 0,
                        ));
                    }

                    throw ClientError::Network->exception(context: ['message' => 'Failed to capture HTTP request'], );
                }

                return new Success(WriteTuplesResponse::fromResponse(
                    $response,
                    $lastRequest,
                    $this->validator,
                ));
            } catch (Throwable $throwable) {
                if ($attempt === $maxRetries) {
                    return new Failure($throwable);
                }

                // Wait before retrying (with exponential backoff)
                if (0 < $retryDelaySeconds && $attempt < $maxRetries) {
                    $delay = $retryDelaySeconds * (float) (2 ** $attempt);
                    usleep((int) ($delay * 1_000_000.0));
                }

                ++$attempt;
            }
        }

        // This should never be reached, but satisfy static analysis
        throw new RuntimeException('Unexpected error in retry logic');
    }

    /**
     * Process a non-transactional write request in chunks.
     *
     * @param WriteTuplesRequest $request The request to process
     *
     * @throws InvalidArgumentException If chunk size is invalid
     *
     * @return SuccessInterface Always returns Success with WriteTuplesResponse
     */
    private function processNonTransactional(WriteTuplesRequest $request): SuccessInterface
    {
        $chunks = $request->chunk($request->getMaxTuplesPerChunk());
        $totalChunks = count($chunks);
        $successfulChunks = 0;
        $failedChunks = 0;

        /** @var array<Throwable> $errors */
        $errors = [];

        // If stop on first error is enabled, process sequentially
        if ($request->getStopOnFirstError()) {
            foreach ($chunks as $chunk) {
                $result = $this->processChunkWithRetries($chunk, $request);

                if ($result instanceof FailureInterface) {
                    ++$failedChunks;
                    $errors[] = $result->err();

                    break; // Stop on first error
                }

                ++$successfulChunks;
            }
        } else {
            // Process chunks in parallel if allowed
            $maxParallel = $request->getMaxParallelRequests();

            if (1 < $maxParallel && 1 < $totalChunks) {
                // Create tasks for parallel execution would go here
                // For now, process sequentially as parallel execution requires RequestManagerFactory

                // For parallel execution, we need to create a custom executor
                // Since ParallelTaskExecutor requires RequestManagerFactory, we'll process sequentially for now
                // TODO: Implement proper parallel execution with HttpService
                foreach ($chunks as $chunk) {
                    $result = $this->processChunkWithRetries($chunk, $request);

                    if ($result instanceof FailureInterface) {
                        ++$failedChunks;
                        $errors[] = $result->err();
                    } else {
                        ++$successfulChunks;
                    }
                }
            } else {
                // Process sequentially
                foreach ($chunks as $chunk) {
                    $result = $this->processChunkWithRetries($chunk, $request);

                    if ($result instanceof FailureInterface) {
                        ++$failedChunks;
                        $errors[] = $result->err();
                    } else {
                        ++$successfulChunks;
                    }
                }
            }
        }

        return new Success(new WriteTuplesResponse(
            transactional: false,
            totalOperations: $request->getTotalOperations(),
            totalChunks: $totalChunks,
            successfulChunks: $successfulChunks,
            failedChunks: $failedChunks,
            errors: $errors,
        ));
    }

    /**
     * Process a write request in either transactional or non-transactional mode.
     *
     * @param WriteTuplesRequest $request The request to process
     *
     * @throws ClientThrowable          If request processing fails
     * @throws InvalidArgumentException If request validation fails
     *
     * @return SuccessInterface Always returns Success with WriteTuplesResponse
     */
    private function processWriteRequest(WriteTuplesRequest $request): SuccessInterface
    {
        // Handle empty requests
        if ($request->isEmpty()) {
            return new Success(new WriteTuplesResponse(
                transactional: $request->isTransactional(),
                totalOperations: 0,
                totalChunks: 0,
                successfulChunks: 0,
                failedChunks: 0,
            ));
        }

        // Transactional mode - single request
        if ($request->isTransactional()) {
            try {
                $response = $this->httpService->send($request);

                $lastRequest = $this->httpService->getLastRequest();

                if (! $lastRequest instanceof RequestInterface) {
                    throw ClientError::Network->exception(context: ['message' => 'Failed to capture HTTP request'], );
                }

                return new Success(WriteTuplesResponse::fromResponse(
                    $response,
                    $lastRequest,
                    $this->validator,
                ));
            } catch (Throwable $throwable) {
                // Return as a failed batch for consistency
                return new Success(new WriteTuplesResponse(
                    transactional: true,
                    totalOperations: $request->getTotalOperations(),
                    totalChunks: 1,
                    successfulChunks: 0,
                    failedChunks: 1,
                    errors: [$throwable],
                ));
            }
        }

        // Non-transactional mode - process in chunks
        return $this->processNonTransactional($request);
    }
}
