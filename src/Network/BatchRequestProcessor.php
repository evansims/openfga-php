<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientThrowable, NetworkException, SerializationException};
use OpenFGA\Requests\WriteTuplesRequest;
use OpenFGA\Responses\WriteTuplesResponse;
use OpenFGA\Results\{Failure, FailureInterface, Success, SuccessInterface};
use OpenFGA\Schema\SchemaValidator;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;
use RuntimeException;
use Throwable;

use function count;

/**
 * Handles batch processing of write tuple requests.
 *
 * This class encapsulates the logic for processing write tuple requests
 * in both transactional and non-transactional modes. It handles chunking,
 * parallel execution, retries, and error aggregation.
 */
final class BatchRequestProcessor
{
    private ?HttpRequestInterface $lastRequest = null;

    private ?HttpResponseInterface $lastResponse = null;

    public function __construct(
        private readonly RequestManagerFactory $requestManagerFactory,
    ) {
    }

    /**
     * Get the last HTTP request made.
     */
    public function getLastRequest(): ?HttpRequestInterface
    {
        return $this->lastRequest;
    }

    /**
     * Get the last HTTP response received.
     */
    public function getLastResponse(): ?HttpResponseInterface
    {
        return $this->lastResponse;
    }

    /**
     * Process a write tuples request.
     *
     * @param  WriteTuplesRequest $request The request to process
     * @return SuccessInterface   Always returns Success with WriteTuplesResponse
     */
    public function process(WriteTuplesRequest $request): SuccessInterface
    {
        try {
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
                return $this->processTransactional($request);
            }

            // Non-transactional mode - process in chunks
            return $this->processNonTransactional($request);
        } catch (Throwable $throwable) {
            // If any exception occurs during batch processing, return it as a failed batch
            $totalChunks = $request->isTransactional() ? 1 : count($request->chunk($request->getMaxTuplesPerChunk()));

            return new Success(new WriteTuplesResponse(
                transactional: $request->isTransactional(),
                totalOperations: $request->getTotalOperations(),
                totalChunks: $totalChunks,
                successfulChunks: 0,
                failedChunks: $totalChunks,
                errors: [$throwable],
            ));
        }
    }

    /**
     * Create a task for processing a single chunk.
     *
     * @param  WriteTuplesRequest                              $chunk
     * @param  WriteTuplesRequest                              $originalRequest
     * @return callable(): (FailureInterface|SuccessInterface)
     */
    private function createChunkTask(WriteTuplesRequest $chunk, WriteTuplesRequest $originalRequest): callable
    {
        return function () use ($chunk, $originalRequest): FailureInterface | SuccessInterface {
            $attempt = 0;
            $maxRetries = $originalRequest->getMaxRetries();
            $retryDelaySeconds = $originalRequest->getRetryDelaySeconds();

            while ($attempt <= $maxRetries) {
                try {
                    $requestManager = $this->requestManagerFactory->createForBatch();
                    $httpRequest = $requestManager->request($chunk);
                    $httpResponse = $requestManager->send($httpRequest);

                    // Update last request/response for debugging
                    $this->lastRequest = $httpRequest;
                    $this->lastResponse = $httpResponse;

                    // Success case: 200 or 204 status codes
                    if (200 === $httpResponse->getStatusCode() || 204 === $httpResponse->getStatusCode()) {
                        return new Success(new WriteTuplesResponse);
                    }

                    // Error case: other status codes should be treated as failures
                    if ($attempt === $maxRetries) {
                        try {
                            RequestManager::handleResponseException($httpResponse, $httpRequest);
                        } catch (Throwable $exception) {
                            return new Failure($exception);
                        }
                    }
                } catch (NetworkException $networkException) {
                    if ($attempt === $maxRetries) {
                        return new Failure($networkException);
                    }
                } catch (Throwable $throwable) {
                    if ($attempt === $maxRetries) {
                        return new Failure($throwable);
                    }
                }

                // Wait before retrying (with exponential backoff)
                if (0 < $retryDelaySeconds && $attempt < $maxRetries) {
                    $delay = $retryDelaySeconds * (float) (2 ** $attempt);
                    usleep((int) ($delay * 1_000_000.0));
                }

                ++$attempt;
            }

            return new Failure(new RuntimeException('Unexpected retry loop exit'));
        };
    }

    /**
     * Process a non-transactional write request in chunks.
     *
     * @param WriteTuplesRequest $request
     *
     * @throws InvalidArgumentException
     */
    private function processNonTransactional(WriteTuplesRequest $request): SuccessInterface
    {
        $chunks = $request->chunk($request->getMaxTuplesPerChunk());

        // Create tasks for each chunk with built-in retry logic
        $tasks = [];

        foreach ($chunks as $chunk) {
            $tasks[] = $this->createChunkTask($chunk, $request);
        }

        // Execute tasks with parallelism
        $executor = new ParallelTaskExecutor($this->requestManagerFactory);
        $results = $executor->execute(
            $tasks,
            $request->getMaxParallelRequests(),
            $request->getStopOnFirstError(),
        );

        // Analyze results
        $errors = [];
        $successfulChunks = 0;
        $failedChunks = 0;

        foreach ($results as $result) {
            if ($result instanceof SuccessInterface) {
                ++$successfulChunks;
            } else {
                $error = $result->err();
                $errors[] = $error;
                ++$failedChunks;
            }
        }

        return new Success(new WriteTuplesResponse(
            transactional: false,
            totalOperations: $request->getTotalOperations(),
            totalChunks: count($chunks),
            successfulChunks: $successfulChunks,
            failedChunks: $failedChunks,
            errors: $errors,
        ));
    }

    /**
     * Process a transactional write request.
     *
     * @param WriteTuplesRequest $request
     *
     * @throws ClientThrowable
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws NetworkException
     * @throws ReflectionException
     * @throws SerializationException
     * @throws Throwable
     */
    private function processTransactional(WriteTuplesRequest $request): SuccessInterface
    {
        $requestManager = $this->requestManagerFactory->create();
        $httpRequest = $requestManager->request($request);
        $httpResponse = $requestManager->send($httpRequest);

        $this->lastRequest = $httpRequest;
        $this->lastResponse = $httpResponse;

        return new Success(WriteTuplesResponse::fromResponse(
            $httpResponse,
            $httpRequest,
            new SchemaValidator,
        ));
    }
}
