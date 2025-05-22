<?php

declare(strict_types=1);

namespace OpenFGA\Results;

use Throwable;

/**
 * @template T
 *
 * @param ResultInterface<T, E> $result
 * @param T $default
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
 * @param ResultInterface<T, E> $result
 * @param null|callable(T): void $fn
 *
 * @return bool
 */
function success(ResultInterface $result, ?callable $fn = null): bool
{
    if ($result->isSuccess()) {
        if ($fn !== null) {
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
 * @param ResultInterface<T, E> $result
 * @param null|callable(E): void $fn
 *
 * @return bool
 */
function failure(ResultInterface $result, ?callable $fn = null): bool
{
    if ($result->isFailure()) {
        if ($fn !== null) {
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
 * @param callable(T): R $onSuccess
 * @param callable(E): R $onFailure
 *
 * @return R
 */
function fold(ResultInterface $result, callable $onSuccess, callable $onFailure): mixed
{
    return $result->isSuccess()
        ? $onSuccess($result->getValue())
        : $onFailure($result->getError());
}
