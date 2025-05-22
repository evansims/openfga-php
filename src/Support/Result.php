<?php

declare(strict_types=1);

namespace OpenFGA\Results;

/**
 * @template T
 * @template E of Throwable
 * @implements ResultInterface<T, E>
 */
abstract class Result implements ResultInterface
{
    public function isSuccess(): bool
    {
        return $this instanceof Success;
    }

    public function isFailure(): bool
    {
        return $this instanceof Failure;
    }

    public function tap(callable $fn): ResultInterface
    {
        if ($this->isSuccess()) {
            $fn($this->getValue());
        }
        return $this;
    }

    public function tapError(callable $fn): ResultInterface
    {
        if ($this->isFailure()) {
            $fn($this->getError());
        }
        return $this;
    }

    public function fold(callable $onSuccess, callable $onFailure): mixed
    {
        return $this->isSuccess()
            ? $onSuccess($this->getValue())
            : $onFailure($this->getError());
    }

    public function unwrap(mixed $default = null): mixed
    {
        return $this->isSuccess() ? $this->getValue() : $default;
    }
}
