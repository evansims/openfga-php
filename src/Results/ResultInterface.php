<?php

declare(strict_types=1);

namespace OpenFGA\Results;

use OpenFGA\Exceptions\ClientThrowable;
use Throwable;

/**
 * Represents the result of an operation that can either succeed or fail.
 *
 * The Result pattern provides a safe and composable way to handle operations that
 * might fail without using exceptions for control flow. Results can be chained
 * together using fluent methods, making error handling explicit and predictable.
 *
 * ## Working with Result Types
 *
 * Each Result contains either a success value (specific response interface) or a failure
 * error (Throwable). The specific types are documented in each method's @return annotation.
 *
 * ## Common Usage Patterns
 *
 * ### Simple Value Extraction
 * ```php
 * $result = $client->check($store, $model, $tupleKey);
 * if ($result->succeeded()) {
 *     $response = $result->val(); // Returns CheckResponseInterface
 *     $allowed = $response->getAllowed();
 * }
 * ```
 *
 * ### Fluent Error Handling
 * ```php
 * $allowed = $client->check($store, $model, $tupleKey)
 *     ->success(fn($response) => logger()->info('Check succeeded'))
 *     ->failure(fn($error) => logger()->error('Check failed: ' . $error->getMessage()))
 *     ->then(fn($response) => $response->getAllowed())
 *     ->recover(fn($error) => false) // Default to not allowed on error
 *     ->unwrap();
 * ```
 *
 * ### Safe Unwrapping with Default Values
 * ```php
 * $store = $client->getStore($storeId)
 *     ->unwrap(fn($result) => $result instanceof StoreInterface ? $result : null);
 * ```
 *
 * ### Transforming Results
 * ```php
 * $storeNames = $client->listStores()
 *     ->then(fn($response) => array_map(
 *         fn($store) => $store->getName(),
 *         $response->getStores()->toArray()
 *     ))
 *     ->unwrap();
 * ```
 */
interface ResultInterface
{
    /**
     * Retrieves the error from a failed result.
     *
     * This method should only be called on Failure results. Use failed() to check
     * the result type before calling this method to avoid exceptions.
     *
     * @throws ClientThrowable When called on a Success result
     *
     * @return Throwable The error that caused the failure
     */
    public function err(): Throwable;

    /**
     * Determines if this result represents a failure.
     *
     * @return bool True if this is a Failure result, false if it's a Success
     */
    public function failed(): bool;

    /**
     * Executes a callback when the result is a failure and continues the chain.
     *
     * The callback receives the error as its parameter and is only executed for
     * Failure results. This method always returns the original result unchanged.
     *
     * @param  callable(Throwable): void $fn Callback to execute with the error on failure
     * @return ResultInterface           The original result for method chaining
     */
    public function failure(callable $fn): self;

    /**
     * Recovers from a failure by transforming it into a success or different failure.
     *
     * The callback is only executed for Failure results and can return either a new
     * Result or a plain value (which becomes a Success). Success results pass through unchanged.
     *
     * @param  callable(Throwable): (mixed|ResultInterface) $fn Recovery function that transforms the error
     * @return ResultInterface                              The recovered result or original success
     */
    public function recover(callable $fn): self;

    /**
     * Throws the contained error or continues the chain.
     *
     * For Failure results, this throws either the provided throwable or the contained error.
     * For Success results, this method has no effect and returns the result unchanged.
     *
     * @param Throwable|null $throwable Optional throwable to throw instead of the contained error
     *
     * @throws Throwable The contained error or provided throwable for Failure results
     *
     * @return ResultInterface The original result for method chaining
     */
    public function rethrow(?Throwable $throwable = null): self;

    /**
     * Determines if this result represents a success.
     *
     * @return bool True if this is a Success result, false if it's a Failure
     */
    public function succeeded(): bool;

    /**
     * Executes a callback when the result is a success and continues the chain.
     *
     * The callback receives the success value (specific response interface) as its parameter
     * and is only executed for Success results. This method always returns the original result unchanged.
     *
     * @param  callable(mixed): void $fn Callback to execute with the response interface on success
     * @return ResultInterface       The original result for method chaining
     */
    public function success(callable $fn): self;

    /**
     * Transforms a successful result using a callback and continues the chain.
     *
     * The callback is only executed for Success results and receives the specific response
     * interface as its parameter. It can return either a new Result or a plain value
     * (which becomes a Success). Failure results pass through unchanged.
     *
     * @param  callable(mixed): (mixed|ResultInterface) $fn Transformation function for the response interface
     * @return ResultInterface                          The transformed result or original failure
     */
    public function then(callable $fn): self;

    /**
     * Extracts the value from the result or applies a transformation.
     *
     * Without a callback, this returns the success value (specific response interface) or throws the failure error.
     * With a callback, the function is called with either the response interface or failure error,
     * and its return value is returned instead of throwing.
     *
     * @param callable(mixed|Throwable): mixed|null $fn Optional transformation function for the result
     *
     * @throws Throwable When called on a Failure result without a callback
     *
     * @return mixed The response interface, callback result, or throws the error
     */
    public function unwrap(?callable $fn = null): mixed;

    /**
     * Retrieves the value from a successful result.
     *
     * This method should only be called on Success results. Use succeeded() to check
     * the result type before calling this method to avoid exceptions. Returns the
     * specific response interface documented in the calling method's @return annotation.
     *
     * @throws ClientThrowable When called on a Failure result
     *
     * @return mixed The response interface (e.g., CheckResponseInterface, StoreInterface)
     */
    public function val(): mixed;
}
