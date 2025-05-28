<?php

declare(strict_types=1);

namespace OpenFGA\Results;

use Closure;
use Throwable;

/**
 * Helper for executing a Closure safely or unwrapping a Result.
 *
 * When passed a Closure, executes it and returns the result wrapped in a Success,
 * or catches any thrown exceptions and wraps them in a Failure object.
 * When passed a ResultInterface, unwraps a Success or throws the error from a Failure.
 *
 * @template T
 * @template E of Throwable
 *
 * @param Closure():ResultInterface<T, E>|Closure():T|ResultInterface<T, E> $context
 *
 * @throws Throwable if a `Failure` ResultInterface is passed and needs to be unwrapped
 *
 * @return ResultInterface<T, Throwable>|T
 */
function result(ResultInterface | Closure $context): mixed
{
    if ($context instanceof Closure) {
        try {
            $out = $context();

            return $out instanceof ResultInterface ? $out : new Success($out);
        } catch (Throwable $t) {
            return new Failure($t);
        }
    }

    if ($context->failed()) {
        /** @var Failure<E> $context */
        throw $context->err();
    }

    /** @var Success<T> $context */
    return $context->val();
}

/**
 * Helper for unwrapping a `Success` or returning a default value.
 *
 * @template T
 * @template E of Throwable
 *
 * @param ResultInterface<T, E>     $result
 * @param null|callable(E|T): mixed $fn
 *
 * @return T
 */
function unwrap(ResultInterface $result, ?callable $fn = null): mixed
{
    return $result->unwrap($fn);
}

/**
 * Helper for executing a callback on a `Success`.
 *
 * @template T
 * @template E of Throwable
 *
 * @param ResultInterface<T, E>  $result
 * @param null|callable(T): void $fn
 */
function success(ResultInterface $result, ?callable $fn = null): bool
{
    if ($result->succeeded()) {
        if (null !== $fn) {
            $fn($result->val());
        }

        return true;
    }

    return false;
}

/**
 * Helper for executing a callback on a `Failure`.
 *
 * @template T
 *
 * @param ResultInterface<T, Throwable>  $result
 * @param null|callable(Throwable): void $fn
 */
function failure(ResultInterface $result, ?callable $fn = null): bool
{
    if ($result->failed()) {
        if (null !== $fn) {
            $fn($result->err());
        }

        return true;
    }

    return false;
}

/**
 * Helper for creating a `Success`.

 *
 * @template T
 *
 * @param T $value
 *
 * @return ResultInterface<T, never>
 */
function ok(mixed $value): ResultInterface
{
    return new Success($value);
}

/**
 * Helper for creating a `Failure`.
 *
 * @template E of Throwable
 *
 * @param E $error
 *
 * @return ResultInterface<never, E>
 */
function err(Throwable $error): ResultInterface
{
    return new Failure($error);
}
