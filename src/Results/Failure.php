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
 *
 * @implements ResultInterface<never, E>
 */
final class Failure extends Result implements ResultInterface
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
     */
    public function recover(callable $fn): ResultInterface
    {
        return $fn($this->err());
    }

    #[Override]
    /**
     * @inheritDoc
     *
     * @return E
     */
    public function err(): Throwable
    {
        return $this->error;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function failed(): bool
    {
        return true;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function failure(callable $fn): ResultInterface
    {
        $fn($this->error);

        return $this;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function succeeded(): bool
    {
        return false;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function success(callable $fn): ResultInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     *
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    #[Override]
    public function then(callable $fn): ResultInterface
    {
        return $this;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function throw(?Throwable $throwable = null): ResultInterface
    {
        throw $throwable ?? $this->error;
    }

    #[Override]
    /**
     * @inheritDoc
     *
     * @return never
     */
    public function val(): never
    {
        throw new LogicException('Failure has no value');
    }
}
