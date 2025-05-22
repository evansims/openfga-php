<?php

declare(strict_types=1);

namespace OpenFGA\Results;

use LogicException;
use Override;
use Throwable;

/**
 * @template E of Throwable
 *
 * @extends Result<never, E>
 */
final class Failure extends Result
{
    /**
     * @param E $error
     */
    public function __construct(private readonly Throwable $error)
    {
    }

    #[Override]
    /**
     * @inheritDoc
     *
     * @return E
     */
    public function getError(): Throwable
    {
        return $this->error;
    }

    #[Override]
    /**
     * @inheritDoc
     *
     * @return never
     */
    public function getValue(): never
    {
        throw new LogicException('Failure has no value');
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function isFailure(): bool
    {
        return true;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function isSuccess(): bool
    {
        return false;
    }

    #[Override]
    /**
     * @template U
     *
     * @param callable(mixed): U $fn
     *
     * @return self<E>
     */
    public function map(callable $fn)
    {
        // On failure, we just return $this without calling the function
        return $this;
    }

    #[Override]
    /**
     * @template F of Throwable
     *
     * @param callable(E): F $fn
     *
     * @return Failure<F>
     */
    public function mapError(callable $fn): ResultInterface
    {
        return new self($fn($this->error));
    }

    #[Override]
    /**
     * @inheritDoc
     */
    /**
     * @param callable(E): void $fn
     *
     * @return $this
     */
    public function onFailure(callable $fn): ResultInterface
    {
        $fn($this->error);

        return $this;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function onSuccess(callable $fn): ResultInterface
    {
        return $this;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function then(callable $fn): ResultInterface
    {
        return $this;
    }

    #[Override]
    /**
     * @template F of Throwable
     *
     * @param F $error
     *
     * @return static<F>
     *
     * @psalm-return Failure<F>
     *
     * @phpstan-return Failure<F>
     */
    public static function createFailure(Throwable $error): static
    {
        return new static($error);
    }
}
