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
 * Concrete implementation of a successful result containing a value.
 *
 * This class represents the successful outcome of an operation, storing the
 * resulting value and providing type-safe access through the Result pattern's
 * fluent interface.
 */
final class Success extends Result implements SuccessInterface
{
    /**
     * Creates a new successful result containing the provided value.
     *
     * @param mixed $value The successful value to store in this result
     */
    public function __construct(private readonly mixed $value)
    {
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          Always throws since successes have no error
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function err(): never
    {
        throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::RESULT_SUCCESS_NO_ERROR)]);
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
     *
     * @throws Throwable Any exception thrown by the transformation callback
     */
    #[Override]
    public function then(callable $fn): ResultInterface
    {
        /** @var mixed $result */
        $result = $fn($this->val());

        if ($result instanceof ResultInterface) {
            return $result;
        }

        return new self($result);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function val(): mixed
    {
        return $this->value;
    }
}
