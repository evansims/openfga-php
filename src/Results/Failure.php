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

    /**
     * @inheritDoc
     *
     * @return E
     */
    #[Override]
    public function err(): Throwable
    {
        return $this->error;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function failed(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function failure(callable $fn): ResultInterface
    {
        $fn($this->error);

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function recover(callable $fn): ResultInterface
    {
        $result = $fn($this->err());

        return $result instanceof ResultInterface ? $result : new Success($result);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function rethrow(?Throwable $throwable = null): ResultInterface
    {
        throw $throwable ?? $this->error;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function succeeded(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    #[Override]
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

    /**
     * @inheritDoc
     */
    #[Override]
    public function val(): never
    {
        throw new LogicException('Failure has no value');
    }
}
