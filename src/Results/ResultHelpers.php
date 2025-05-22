<?php

declare(strict_types=1);

namespace OpenFGA\Results;

use Throwable;

/**
 * @template T
 * @template E of Throwable
 *
 * @param ResultInterface<T, E> $result
 * @param T                     $default
 *
 * @return T
 */
function unwrap(ResultInterface $result, mixed $default = null): mixed
{
    return $result->unwrap($default);
}

/**
 * @template T
 * @template E of Throwable
 *
 * @param ResultInterface<T, E>  $result
 * @param null|callable(T): void $fn
 */
function success(ResultInterface $result, ?callable $fn = null): bool
{
    if ($result->isSuccess()) {
        if (null !== $fn) {
            $fn($result->getValue());
        }

        return true;
    }

    return false;
}

/**
 * @template T
 * @template E of Throwable
 *
 * @param ResultInterface<T, E>  $result
 * @param null|callable(E): void $fn
 */
function failure(ResultInterface $result, ?callable $fn = null): bool
{
    if ($result->isFailure()) {
        if (null !== $fn) {
            $fn($result->getError());
        }

        return true;
    }

    return false;
}

/**
 * @template T
 * @template E of Throwable
 * @template R
 *
 * @param ResultInterface<T, E> $result
 * @param callable(T): R        $onSuccess
 * @param callable(E): R        $onFailure
 *
 * @return R
 */
function fold(ResultInterface $result, callable $onSuccess, callable $onFailure): mixed
{
    return $result->isSuccess()
        ? $onSuccess($result->getValue())
        : $onFailure($result->getError());
}
