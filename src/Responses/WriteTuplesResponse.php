<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientThrowable, NetworkException, SerializationException};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schemas\SchemaValidator;
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;
use RuntimeException;
use Throwable;

use function sprintf;

/**
 * Response for tuple writing operations supporting both transactional and non-transactional modes.
 *
 * This response handles results from both transactional writes (all-or-nothing) and
 * non-transactional writes (independent operations with detailed tracking).
 *
 * @see WriteTuplesResponseInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Tuples/Write
 */
final class WriteTuplesResponse extends Response implements WriteTuplesResponseInterface
{
    /**
     * Create a new WriteTuplesResponse.
     *
     * @param bool             $transactional    Whether the operation was transactional
     * @param int              $totalOperations  Total number of operations
     * @param int              $totalChunks      Total number of chunks processed
     * @param int              $successfulChunks Number of successful chunks
     * @param int              $failedChunks     Number of failed chunks
     * @param array<Throwable> $errors           Errors from failed operations
     */
    public function __construct(
        private readonly bool $transactional = true,
        private readonly int $totalOperations = 0,
        private readonly int $totalChunks = 1,
        private readonly int $successfulChunks = 1,
        private readonly int $failedChunks = 0,
        /** @var array<Throwable> */
        private readonly array $errors = [],
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws NetworkException         If the API returns an error response
     * @throws ReflectionException      If exception location capture fails
     * @throws SerializationException   If JSON parsing or schema validation fails
     */
    #[Override]
    public static function fromResponse(
        HttpResponseInterface $response,
        HttpRequestInterface $request,
        SchemaValidator $validator,
    ): WriteTuplesResponseInterface {
        if (200 === $response->getStatusCode() || 204 === $response->getStatusCode()) {
            // Simple transactional success
            return new self;
        }

        RequestManager::handleResponseException(
            response: $response,
            request: $request,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getFailedChunks(): int
    {
        return $this->failedChunks;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getFirstError(): ?Throwable
    {
        return $this->errors[0] ?? null;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getSuccessfulChunks(): int
    {
        return $this->successfulChunks;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getSuccessRate(): float
    {
        if (0 === $this->totalChunks) {
            return 0.0;
        }

        return $this->successfulChunks / $this->totalChunks;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTotalChunks(): int
    {
        return $this->totalChunks;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTotalOperations(): int
    {
        return $this->totalOperations;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function isCompleteFailure(): bool
    {
        return 0 === $this->successfulChunks && 0 < $this->totalChunks;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function isCompleteSuccess(): bool
    {
        return 0 === $this->failedChunks && 0 < $this->totalChunks;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function isPartialSuccess(): bool
    {
        return 0 < $this->successfulChunks && 0 < $this->failedChunks;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function isTransactional(): bool
    {
        return $this->transactional;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function throwOnFailure(): void
    {
        if (0 === $this->failedChunks) {
            return;
        }

        $firstError = $this->getFirstError();

        if ($firstError instanceof ClientThrowable) {
            throw $firstError;
        }

        if ($firstError instanceof Throwable) {
            throw $firstError;
        }

        throw new RuntimeException(sprintf('Batch operation failed: %d of %d chunks failed', $this->failedChunks, $this->totalChunks));
    }
}
