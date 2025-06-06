<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schemas\SchemaInterface;
use Override;
use Throwable;

/**
 * Interface for batch tuple operation results.
 *
 * Defines the contract for tracking and analyzing the results of batch
 * tuple operations, including success rates, responses, and error handling.
 */
interface BatchTupleResultInterface extends ModelInterface
{
    /**
     * Get the JSON schema for this model.
     *
     * @return SchemaInterface The schema definition
     */
    #[Override]
    public static function schema(): SchemaInterface;

    /**
     * Get all errors from failed chunks.
     *
     * @return array<Throwable> Errors from failed API calls
     */
    public function getErrors(): array;

    /**
     * Get the number of chunks that failed.
     *
     * @return int Number of failed API requests
     */
    public function getFailedChunks(): int;

    /**
     * Get the first error that occurred.
     *
     * @return Throwable|null The first error, or null if no errors occurred
     */
    public function getFirstError(): ?Throwable;

    /**
     * Get all successful responses from completed chunks.
     *
     * @return array<mixed> Responses from successful API calls
     */
    public function getResponses(): array;

    /**
     * Get the number of chunks that completed successfully.
     *
     * @return int Number of successful API requests
     */
    public function getSuccessfulChunks(): int;

    /**
     * Calculate the success rate as a percentage.
     *
     * @return float Success rate from 0.0 to 1.0
     */
    public function getSuccessRate(): float;

    /**
     * Get the total number of chunks that were processed.
     *
     * @return int Number of API requests made
     */
    public function getTotalChunks(): int;

    /**
     * Get the total number of tuple operations that were requested.
     *
     * @return int Total operations across all chunks
     */
    public function getTotalOperations(): int;

    /**
     * Check if all chunks failed.
     *
     * @return bool True if no chunks succeeded
     */
    public function isCompleteFailure(): bool;

    /**
     * Check if all chunks completed successfully.
     *
     * @return bool True if no chunks failed
     */
    public function isCompleteSuccess(): bool;

    /**
     * Check if some chunks succeeded and some failed.
     *
     * @return bool True if there were both successes and failures
     */
    public function isPartialSuccess(): bool;

    /**
     * Throw an exception if any chunks failed.
     *
     * If there were failures, throws the first error that occurred.
     * This is useful for treating partial failures as complete failures
     * when strict error handling is required.
     *
     * @throws Throwable The first error that occurred
     */
    public function throwOnFailure(): void;
}
