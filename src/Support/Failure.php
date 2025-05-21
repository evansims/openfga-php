<?php

declare(strict_types=1);

namespace OpenFGA\Support;

use LogicException;
use Override;
use Throwable;

/**
 * @template E of Throwable
 * @extends Result<never, E>
 */
final class Failure extends Result
{
    public function __construct(private readonly Throwable $error) {}

    #[Override]
    /**
     * @inheritDoc
     */
    public function getValue(): never
    {
        throw new LogicException('Failure has no value');
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getError(): Throwable
    {
        return $this->error;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function map(callable $fn): ResultInterface
    {
        return $this;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function mapError(callable $fn): ResultInterface
    {
        return new Failure($fn($this->error));
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
     * @inheritDoc
     */
    public static function createFailure(Throwable $error): ResultInterface
    {
        return new self($error);
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function onSuccess(callable $fn): self
    {
        return $this;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function onFailure(callable $fn): self
    {
        $fn($this->error);
        return $this;
    }
}
