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

    #[Override]
    /**
     * @inheritDoc
     *
     * @return never
     */
    public function err(): never
    {
        throw new LogicException('Success has no error');
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function failed(): bool
    {
        return false;
    }

    #[Override]
    /**
     * @inheritDoc
     */
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

    #[Override]
    /**
     * @inheritDoc
     */
    public function rethrow(?Throwable $throwable = null): ResultInterface
    {
        return $this;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function succeeded(): bool
    {
        return true;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function success(callable $fn): ResultInterface
    {
        $fn($this->value);

        return $this;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function then(callable $fn): ResultInterface
    {
        $result = $fn($this->val());

        return $result instanceof ResultInterface ? $result : new self($result);
    }

    #[Override]
    /**
     * @inheritDoc
     *
     * @return T
     */
    public function val(): mixed
    {
        return $this->value;
    }
}
