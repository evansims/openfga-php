<?php

declare(strict_types=1);

namespace OpenFGA\Results;

use LogicException;
use Throwable;

/**
 * @template T
 * @template E of Throwable
 */
interface ResultInterface
{
    /**
     * @template R
     *
     * @param callable(T): R $onSuccess
     * @param callable(E): R $onFailure
     *
     * @return R
     */
    public function fold(callable $onSuccess, callable $onFailure): mixed;

    /**
     * @throws LogicException if called on Success
     *
     * @return E
     */
    public function getError(): Throwable;

    /**
     * @throws LogicException if called on Failure
     *
     * @return T
     */
    public function getValue(): mixed;

    /**
     * @phpstan-assert-if-true Failure<E> $this
     */
    public function isFailure(): bool;

    /**
     * @phpstan-assert-if-true Success<T, E> $this
     */
    public function isSuccess(): bool;

    /**
     * @template U
     *
     * @param callable(T): U $fn
     *
     * @return ResultInterface<U, E>
     */
    public function map(callable $fn): ResultInterface;

    /**
     * @template F of Throwable
     *
     * @param callable(E): F $fn
     *
     * @return ResultInterface<T, F>
     */
    public function mapError(callable $fn): ResultInterface;

    /**
     * @param callable(E): void $fn
     *
     * @return ResultInterface<T, E>
     */
    public function onFailure(callable $fn): ResultInterface;

    /**
     * @param callable(T): void $fn
     *
     * @return ResultInterface<T, E>
     */
    public function onSuccess(callable $fn): ResultInterface;

    /**
     * @param callable(T): void $fn
     *
     * @return ResultInterface<T, E>
     */
    public function tap(callable $fn): ResultInterface;

    /**
     * @param callable(E): void $fn
     *
     * @return ResultInterface<T, E>
     */
    public function tapError(callable $fn): ResultInterface;

    /**
     * @template U
     *
     * @param callable(T): ResultInterface<U, E> $fn
     *
     * @return ResultInterface<U, E>
     */
    public function then(callable $fn): ResultInterface;

    /**
     * @template R
     *
     * @param R $default
     *
     * @return R|T
     */
    public function unwrap(mixed $default = null): mixed;
}
