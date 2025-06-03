<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Throwable;

/**
 * Interface for tuple writing response objects.
 *
 * This interface defines the contract for responses returned when writing relationship
 * tuples to an OpenFGA store. The response handles both transactional and non-transactional
 * write modes, providing appropriate feedback for each operation type.
 *
 * In transactional mode, all changes succeed or fail together. In non-transactional mode,
 * operations are processed independently with detailed success/failure tracking.
 *
 * @see https://openfga.dev/docs/interacting/managing-relationships-between-objects OpenFGA Write Tuples API Documentation
 */
interface WriteTuplesResponseInterface extends ResponseInterface
{
    /**
     * Get all errors that occurred during processing.
     *
     * @return array<Throwable> Array of exceptions from failed operations
     */
    public function getErrors(): array;

    /**
     * Get the number of failed chunks.
     *
     * @return int The number of failed chunks
     */
    public function getFailedChunks(): int;

    /**
     * Get the first error that occurred.
     *
     * @return Throwable|null The first error, or null if no errors
     */
    public function getFirstError(): ?Throwable;

    /**
     * Get the number of successfully processed chunks.
     *
     * @return int The number of successful chunks
     */
    public function getSuccessfulChunks(): int;

    /**
     * Calculate the success rate of the operation.
     *
     * @return float Success rate between 0.0 and 1.0
     */
    public function getSuccessRate(): float;

    /**
     * Get the total number of chunks processed (non-transactional mode).
     *
     * @return int The number of chunks, or 1 for transactional mode
     */
    public function getTotalChunks(): int;

    /**
     * Get the total number of tuple operations processed.
     *
     * @return int The total number of write and delete operations
     */
    public function getTotalOperations(): int;

    /**
     * Check if all operations failed.
     *
     * @return bool True if all operations failed
     */
    public function isCompleteFailure(): bool;

    /**
     * Check if all operations completed successfully.
     *
     * @return bool True if all operations succeeded
     */
    public function isCompleteSuccess(): bool;

    /**
     * Check if some operations succeeded and some failed.
     *
     * @return bool True if partial success (non-transactional mode only)
     */
    public function isPartialSuccess(): bool;

    /**
     * Check if the operation was executed in transactional mode.
     *
     * @return bool True if transactional, false if non-transactional
     */
    public function isTransactional(): bool;

    /**
     * Throw an exception if any operations failed.
     *
     * @throws Throwable The first error that occurred
     */
    public function throwOnFailure(): void;
}
