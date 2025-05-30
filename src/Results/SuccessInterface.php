<?php

declare(strict_types=1);

namespace OpenFGA\Results;

/**
 * Represents a successful result containing a value.
 *
 * Success results indicate that an operation completed successfully and contain
 * a value of the specified type. They provide type-safe access to successful
 * outcomes while maintaining compatibility with the Result pattern's fluent interface.
 *
 * Success results behave predictably in all Result operations:
 * - `succeeded()` always returns true
 * - `failed()` always returns false
 * - `val()` returns the contained value safely
 * - `err()` throws since successes have no errors
 * - `success()` executes callbacks with the value
 * - `failure()` skips callbacks and returns unchanged
 * - `then()` applies transformations to the value
 * - `recover()` skips recovery and returns unchanged
 */
interface SuccessInterface extends ResultInterface
{
}
