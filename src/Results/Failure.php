<?php

declare(strict_types=1);

namespace OpenFGA\Results;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Translation\Translator;
use Override;
use ReflectionException;
use Throwable;

/**
 * Concrete implementation of a failed result containing an error.
 *
 * This class represents the failed outcome of an operation, storing the
 * error that caused the failure and providing safe access through the
 * Result pattern's fluent interface.
 */
final class Failure extends Result implements FailureInterface
{
    /**
     * Creates a new failed result containing the provided error.
     *
     * @param Throwable $error The error that caused the failure
     */
    public function __construct(private readonly Throwable $error)
    {
    }

    /**
     * @inheritDoc
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
     *
     * @throws Throwable Any exception thrown by the recovery callback
     */
    #[Override]
    public function recover(callable $fn): ResultInterface
    {
        /** @var mixed $result */
        $result = $fn($this->err());

        if ($result instanceof ResultInterface) {
            return $result;
        }

        return new Success($result);
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
     */
    #[Override]
    public function then(callable $fn): ResultInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          Always throws since failures have no value
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function val(): never
    {
        throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::RESULT_FAILURE_NO_VALUE)]);
    }
}
