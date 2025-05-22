<?php

declare(strict_types=1);

namespace OpenFGA\Results;

use LogicException;
use Override;
use Throwable;

/**
 * @template T
 * @template E of Throwable
 *
 * @extends Result<T, never>
 *
 * @implements ResultInterface<T, never>
 */
final class Success extends Result implements ResultInterface
{
    /**
     * @param T $value
     */
    public function __construct(private readonly mixed $value)
    {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getError(): never
    {
        throw new LogicException('Success has no error');
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function isFailure(): bool
    {
        return false;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function isSuccess(): bool
    {
        return true;
    }

    #[Override]
    /**
     * @template U
     *
     * @param callable(T): U $fn
     *
     * @return Success<U, never>
     */
    public function map(callable $fn): ResultInterface
    {
        $mappedValue = $fn($this->value);

        return new self($mappedValue);
    }

    #[Override]
    /**
     * @template F of Throwable
     *
     * @param callable(never): F $fn
     *
     * @return self<T, E>
     */
    public function mapError(callable $fn): ResultInterface
    {
        return $this;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function onFailure(callable $fn): ResultInterface
    {
        return $this;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function onSuccess(callable $fn): ResultInterface
    {
        $fn($this->value);

        return $this;
    }

    #[Override]
    /**
     * @template U
     *
     * @param callable(T): ResultInterface<U, E> $fn
     *
     * @return ResultInterface<U, E>
     */
    public function then(callable $fn): ResultInterface
    {
        return $fn($this->value);
    }
}
