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
     * Return the unwrapped error of a `Failure`.
     *
     * @throws LogicException if called on Success
     */
    public function err(): Throwable;

    /**
     * Return `true` if this is a `Failure`.
     */
    public function failed(): bool;

    /**
     * Execute on `Failure` and continue the chain.
     *
     * @param callable(E): void $fn
     *
     * @return ResultInterface<T, E>
     */
    public function failure(callable $fn): self;

    /**
     * Execute on `Failure`, mutate the result, and continue the chain.
     *
     * @template U
     * @template F of Throwable
     *
     * @param callable(Throwable): (ResultInterface<U, F>|U) $fn
     *
     * @return ResultInterface<U, F>
     */
    public function recover(callable $fn): self;

    /**
     * Throw the error of a `Failure`, or continue the chain.
     *
     * @param ?Throwable $throwable
     *
     * @return ResultInterface<T, E>
     */
    public function rethrow(?Throwable $throwable = null): self;

    /**
     * Return `true` if this is a `Success`.
     */
    public function succeeded(): bool;

    /**
     * Execute on `Success` and continue the chain.
     *
     * @param callable(T): void $fn
     *
     * @return ResultInterface<T, E>
     */
    public function success(callable $fn): self;

    /**
     * Execute on `Success`, mutate the result, and continue the chain.
     *
     * @template U
     * @template F of Throwable
     *
     * @param callable(T): (ResultInterface<U, F>|U) $fn
     *
     * @return ResultInterface<U, F>
     */
    public function then(callable $fn): self;

    /**
     * Return the unwrapped value of a `Success`, or throws the error of a `Failure`.
     * When a callable is provided, it is called with the value of the `Success` or `Failure`, and its return value is returned.
     *
     * @param null|callable(E|T): mixed $fn
     */
    public function unwrap(?callable $fn = null): mixed;

    /**
     * Return the unwrapped value of a `Success`.
     *
     * @throws LogicException if called on Failure
     */
    public function val(): mixed;
}
