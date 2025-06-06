<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use const JSON_THROW_ON_ERROR;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Models\Collections\{TupleKeys, TupleKeysInterface};
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionException;

/**
 * Request for writing and deleting relationship tuples in OpenFGA.
 *
 * This request enables batch creation and deletion of relationship tuples,
 * supporting both transactional (all-or-nothing) and non-transactional
 * (independent operations) modes. Transactional mode ensures atomic changes,
 * while non-transactional mode allows for parallel processing with detailed
 * success/failure tracking.
 *
 * @see WriteTuplesRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Tuples/Write Tuple write API endpoint
 */
final readonly class WriteTuplesRequest implements WriteTuplesRequestInterface
{
    private int $maxParallelRequests;

    private int $maxRetries;

    private int $maxTuplesPerChunk;

    private float $retryDelaySeconds;

    /**
     * @param string                  $store               The store ID
     * @param string                  $model               Authorization model ID
     * @param TupleKeysInterface|null $writes              Tuples to write (optional)
     * @param TupleKeysInterface|null $deletes             Tuples to delete (optional)
     * @param bool                    $transactional       Whether to use transactional mode (default: true)
     * @param int                     $maxParallelRequests Maximum parallel requests for non-transactional mode (default: 1)
     * @param int                     $maxTuplesPerChunk   Maximum tuples per chunk for non-transactional mode (default: 100)
     * @param int                     $maxRetries          Maximum retries for failed chunks (default: 0)
     * @param float                   $retryDelaySeconds   Retry delay in seconds (default: 1.0)
     * @param bool                    $stopOnFirstError    Stop processing on first error (default: false)
     *
     * @throws ClientThrowable          If the store ID or model ID is empty
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $store,
        private string $model,
        private ?TupleKeysInterface $writes = null,
        private ?TupleKeysInterface $deletes = null,
        private bool $transactional = true,
        int $maxParallelRequests = 1,
        int $maxTuplesPerChunk = 100,
        int $maxRetries = 0,
        float $retryDelaySeconds = 1.0,
        private bool $stopOnFirstError = false,
    ) {
        if ('' === $this->store) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_ID_EMPTY)]);
        }

        if ('' === $this->model) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_MODEL_ID_EMPTY)]);
        }

        // Normalize parameters during initialization
        $this->maxParallelRequests = max(1, $maxParallelRequests);
        $this->maxTuplesPerChunk = max(1, min($maxTuplesPerChunk, 100));
        $this->maxRetries = max(0, $maxRetries);
        $this->retryDelaySeconds = max(0.0, $retryDelaySeconds);
    }

    /**
     * Split this request into smaller chunks for non-transactional processing.
     *
     * @param int $chunkSize Maximum tuples per chunk (1-100)
     *
     * @throws InvalidArgumentException If chunk size is invalid
     *
     * @return array<WriteTuplesRequest> Array of chunked requests
     */
    public function chunk(int $chunkSize): array
    {
        // Validate chunk size
        if (0 >= $chunkSize) {
            throw new InvalidArgumentException('Chunk size must be a positive integer');
        }

        if (100 < $chunkSize) {
            throw new InvalidArgumentException('Chunk size cannot exceed 100');
        }

        // Empty operations should return empty array
        if ($this->isEmpty()) {
            return [];
        }

        // If no chunking needed, return self
        if ($this->getTotalOperations() <= $chunkSize) {
            return [$this];
        }

        $chunks = [];

        /** @var array<TupleKeyInterface> $writes */
        $writes = $this->writes instanceof TupleKeysInterface ? [...$this->writes] : [];

        /** @var array<TupleKeyInterface> $deletes */
        $deletes = $this->deletes instanceof TupleKeysInterface ? [...$this->deletes] : [];

        while ([] !== $writes || [] !== $deletes) {
            /** @var array<TupleKeyInterface> $chunkWrites */
            $chunkWrites = [];

            /** @var array<TupleKeyInterface> $chunkDeletes */
            $chunkDeletes = [];
            $remaining = $chunkSize;

            // Take writes first, up to the remaining capacity
            while ([] !== $writes && 0 < $remaining) {
                $chunkWrites[] = array_shift($writes);
                --$remaining;
            }

            // Take deletes with remaining capacity
            while ([] !== $deletes && 0 < $remaining) {
                $chunkDeletes[] = array_shift($deletes);
                --$remaining;
            }

            $chunks[] = new self(
                store: $this->store,
                model: $this->model,
                writes: [] !== $chunkWrites ? new TupleKeys($chunkWrites) : null,
                deletes: [] !== $chunkDeletes ? new TupleKeys($chunkDeletes) : null,
                transactional: false, // Chunks are always non-transactional
                maxParallelRequests: $this->maxParallelRequests,
                maxTuplesPerChunk: $this->maxTuplesPerChunk,
                maxRetries: $this->maxRetries,
                retryDelaySeconds: $this->retryDelaySeconds,
                stopOnFirstError: $this->stopOnFirstError,
            );
        }

        return $chunks;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getDeletes(): ?TupleKeysInterface
    {
        return $this->deletes;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getMaxParallelRequests(): int
    {
        return $this->maxParallelRequests;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getMaxTuplesPerChunk(): int
    {
        return $this->maxTuplesPerChunk;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = array_filter([
            'authorization_model_id' => $this->model,
            'writes' => $this->writes?->jsonSerialize(),
            'deletes' => $this->deletes?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/write',
            body: $stream,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRetryDelaySeconds(): float
    {
        return $this->retryDelaySeconds;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getStopOnFirstError(): bool
    {
        return $this->stopOnFirstError;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }

    /**
     * Get the total number of tuple operations in this request.
     *
     * @return int Total count of write and delete operations
     */
    public function getTotalOperations(): int
    {
        $writeCount = $this->writes?->count() ?? 0;
        $deleteCount = $this->deletes?->count() ?? 0;

        return $writeCount + $deleteCount;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getWrites(): ?TupleKeysInterface
    {
        return $this->writes;
    }

    /**
     * Check if this request contains any operations.
     *
     * @return bool True if the request has no operations
     */
    public function isEmpty(): bool
    {
        return 0 === $this->getTotalOperations();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function isTransactional(): bool
    {
        return $this->transactional;
    }
}
