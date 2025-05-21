<?php

declare(strict_types=1);

namespace OpenFGA\Support;

use LogicException;
use Throwable;

/**
 * @template T
 * @template E of Throwable
 */
interface ResultInterface
{
    /**
     * @template U
     * @param callable(T): ResultInterface<U, E> $fn
     * @return ResultInterface<U, E>
     */
    public function then(callable $fn): ResultInterface;

    /**
     * @param callable(T): void $fn
     * @return ResultInterface<T, E>
     */
    public function tap(callable $fn): ResultInterface;

    /**
     * @param callable(E): void $fn
     * @return ResultInterface<T, E>
     */
    public function tapError(callable $fn): ResultInterface;

    /**
     * @template R
     * @param R $default
     * @return T|R
     */
    public function unwrap(mixed $default = null): mixed;

    /**
     * @throws LogicException if called on Success
     *
     * @return E
     */
    public function getError();

    /**
     * @throws LogicException if called on Failure
     *
     * @return T
     */
    public function getValue();

    /**
     * @return bool
     *
     * @psalm-assert-if-true E $this
     */
    public function isFailure(): bool;

    /**
     * @return bool
     *
     * @psalm-assert-if-true T $this
     */
    public function isSuccess(): bool;

    /**
     * @param callable(T): void $fn
     *
     * @return self
     */
    public function onSuccess(callable $fn): self;

    /**
     * @param callable(E): void $fn
     *
     * @return self
     */
    public function onFailure(callable $fn): self;

    /**
     * @template U
     * @param callable(T): U $fn
     * @return ResultInterface<U, E>
     */
    public function map(callable $fn): self;


    /**
     * @template F of Throwable
     * @param callable(E): F $fn
     * @return ResultInterface<T, F>
     */
    public function mapError(callable $fn): ResultInterface;

    /**
     * @template R
     * @param callable(T): R $onSuccess
     * @param callable(E): R $onFailure
     * @return R
     */
    public function fold(callable $onSuccess, callable $onFailure): mixed;

    /**
     * @param Throwable $error
     *
     * @return ResultInterface<T, Throwable>
     */
    public static function createFailure(Throwable $error): ResultInterface;
}
