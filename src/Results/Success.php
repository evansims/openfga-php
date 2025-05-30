<?php

declare(strict_types=1);

namespace OpenFGA\Results;

use LogicException;
use Override;
use Throwable;

/**
 * @template T
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

    /**
     * @inheritDoc
     */
    #[Override]
    public function err(): never
    {
        throw new LogicException('Success has no error');
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function failed(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function failure(callable $fn): ResultInterface
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
    public function recover(callable $fn): ResultInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function rethrow(?Throwable $throwable = null): ResultInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function succeeded(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function success(callable $fn): ResultInterface
    {
        $fn($this->value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function then(callable $fn): ResultInterface
    {
        $result = $fn($this->val());

        return $result instanceof ResultInterface ? $result : new self($result);
    }

    /**
     * @inheritDoc
     *
     * @return T
     */
    #[Override]
    public function val(): mixed
    {
        return $this->value;
    }
}
