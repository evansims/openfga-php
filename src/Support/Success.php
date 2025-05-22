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
 * @extends Result<T, E>
 */
final class Success extends Result
{
    /**
     * @inheritDoc
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
     * @return static<U, E>
     *
     * @psalm-return Success<U, E>
     *
     * @phpstan-return Success<U, E>
     */
    public function map(callable $fn): static
    {
        // Call the function with the current value
        $mappedValue = $fn($this->value);

        // Create a new Success with the mapped value
        return new static($mappedValue);
    }

    #[Override]
    /**
     * @inheritDoc
     */
    /**
     * @template F of Throwable
     *
     * @param callable(never): F $fn
     *
     * @return $this
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
     * @inheritDoc
     */
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

    #[Override]
    /**
     * @inheritDoc
     */
    public static function createFailure(Throwable $error): static
    {
        throw new LogicException('Cannot create failure from Success');
    }
}
