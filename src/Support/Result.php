<?php

declare(strict_types=1);

namespace OpenFGA\Results;

use Override;
use Throwable;

/**
 * @template T
 * @template E of Throwable
 *
 * @implements ResultInterface<T, E>
 */
abstract class Result implements ResultInterface
{
    #[Override]
    /**
     * @template R
     *
     * @param callable(T): R $onSuccess
     * @param callable(E): R $onFailure
     *
     * @return R
     */
    public function fold(callable $onSuccess, callable $onFailure): mixed
    {
        return $this->isSuccess()
            ? $onSuccess($this->getValue())
            : $onFailure($this->getError());
    }

    #[Override]
    /**
     * @param callable(T): void $fn
     *
     * @return $this
     */
    public function tap(callable $fn): ResultInterface
    {
        if ($this->isSuccess()) {
            $fn($this->getValue());
        }

        return $this;
    }

    #[Override]
    /**
     * @param callable(E): void $fn
     *
     * @return $this
     */
    public function tapError(callable $fn): ResultInterface
    {
        if ($this->isFailure()) {
            $fn($this->getError());
        }

        return $this;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function unwrap(mixed $default = null): mixed
    {
        return $this->isSuccess() ? $this->getValue() : $default;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    abstract public function isFailure(): bool;

    #[Override]
    /**
     * @inheritDoc
     */
    abstract public function isSuccess(): bool;

    #[Override]
    /**
     * @template U
     *
     * @param callable(T): U $fn
     *
     * @return static<U, E>
     */
    abstract public function map(callable $fn);
}
