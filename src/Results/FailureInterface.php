<?php

declare(strict_types=1);

namespace OpenFGA\Results;

use Throwable;

/**
 * Represents a failed result containing an error.
 *
 * Failure results indicate that an operation encountered an error and contain
 * the throwable that caused the failure. They provide safe access to error
 * information while maintaining compatibility with the Result pattern's fluent interface.
 *
 * Failure results behave predictably in all Result operations:
 * - `succeeded()` always returns false
 * - `failed()` always returns true
 * - `err()` returns the contained error safely
 * - `val()` throws since failures have no values
 * - `failure()` executes callbacks with the error
 * - `success()` skips callbacks and returns unchanged
 * - `then()` skips transformations and returns unchanged
 * - `recover()` applies recovery functions to the error
 * - `rethrow()` throws the error or provided throwable
 */
interface FailureInterface extends ResultInterface
{
}
