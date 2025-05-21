<?php

declare(strict_types=1);

namespace OpenFGA\Support;

use LogicException;
use Override;
use Throwable;

/**
 * @template T
 * @extends Result<T, never>
 */
final class Success extends Result
{
    public function __construct(private readonly mixed $value) {}

    #[Override]
    /**
     * @inheritDoc
     */
    public function getValue(): mixed { return $this->value; }

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
    public function map(callable $fn): ResultInterface
    {
        return new Success($fn($this->value));
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function mapError(callable $fn): ResultInterface
    {
        return $this;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function then(callable $fn): ResultInterface
    {
        return $fn($this->value);
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public static function createFailure(Throwable $error): ResultInterface
    {
        throw new LogicException('Cannot create failure from Success');
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function onSuccess(callable $fn): self
    {
        $fn($this->value);
        return $this;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function onFailure(callable $fn): self
    {
        return $this;
    }
}
