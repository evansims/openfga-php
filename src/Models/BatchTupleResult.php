<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Exceptions\ClientThrowable;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;
use RuntimeException;
use Throwable;

use function sprintf;

/**
 * Represents the result of a batch tuple operation.
 *
 * This model tracks the results of processing a batch of tuple operations,
 * including successful chunks, failed chunks, and overall statistics.
 * It provides methods to analyze the success rate and retrieve details
 * about any failures that occurred during processing.
 *
 * @see BatchTupleOperationInterface
 */
final class BatchTupleResult implements BatchTupleResultInterface
{
    public const string OPENAPI_MODEL = 'BatchTupleResult';

    private static ?SchemaInterface $schema = null;

    /**
     * Create a new batch tuple result.
     *
     * @param int              $totalOperations  Total number of tuple operations requested
     * @param int              $totalChunks      Total number of chunks processed
     * @param int              $successfulChunks Number of chunks that completed successfully
     * @param int              $failedChunks     Number of chunks that failed
     * @param array<mixed>     $responses        Successful responses from completed chunks
     * @param array<Throwable> $errors           Errors from failed chunks
     */
    public function __construct(
        private readonly int $totalOperations,
        private readonly int $totalChunks,
        private readonly int $successfulChunks,
        private readonly int $failedChunks,
        /** @var array<mixed> */
        private readonly array $responses = [],
        /** @var array<Throwable> */
        private readonly array $errors = [],
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'totalOperations', type: 'integer', required: true),
                new SchemaProperty(name: 'totalChunks', type: 'integer', required: true),
                new SchemaProperty(name: 'successfulChunks', type: 'integer', required: true),
                new SchemaProperty(name: 'failedChunks', type: 'integer', required: true),
                new SchemaProperty(name: 'successRate', type: 'number', required: true),
                new SchemaProperty(name: 'isCompleteSuccess', type: 'boolean', required: true),
                new SchemaProperty(name: 'isCompleteFailure', type: 'boolean', required: true),
                new SchemaProperty(name: 'isPartialSuccess', type: 'boolean', required: true),
            ],
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
    public function getResponses(): array
    {
        return $this->responses;
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
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'totalOperations' => $this->totalOperations,
            'totalChunks' => $this->totalChunks,
            'successfulChunks' => $this->successfulChunks,
            'failedChunks' => $this->failedChunks,
            'successRate' => $this->getSuccessRate(),
            'isCompleteSuccess' => $this->isCompleteSuccess(),
            'isCompleteFailure' => $this->isCompleteFailure(),
            'isPartialSuccess' => $this->isPartialSuccess(),
        ];
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

        throw new RuntimeException(sprintf('Batch operation failed: %s of %d chunks failed', $this->failedChunks, $this->totalChunks));
    }
}
