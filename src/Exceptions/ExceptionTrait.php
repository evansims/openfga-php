<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

/**
 * Trait providing common implementation for ClientThrowable interface methods.
 *
 * This trait implements the standard methods required by the ClientThrowable interface,
 * providing default behavior for accessing exception context, error categories, and
 * associated HTTP request/response information. It should be used by all concrete
 * exception classes in the OpenFGA SDK to ensure consistent behavior.
 *
 * The trait expects the using class to have the following private readonly properties:
 * - array $context: Additional context data for the exception
 * - ClientError|AuthenticationError|ConfigurationError|NetworkError|SerializationError $kind: The error category
 * - ?RequestInterface $request: The associated HTTP request
 * - ?ResponseInterface $response: The associated HTTP response
 * - ?Throwable $previous: The previous exception in the chain
 */
trait ExceptionTrait
{
    /**
     * @inheritDoc
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return $this->context;
    }

    /**
     * @inheritDoc
     */
    public function kind(): ClientError | AuthenticationError | ConfigurationError | NetworkError | SerializationError
    {
        return $this->kind;
    }

    /**
     * @inheritDoc
     */
    public function previous(): ?Throwable
    {
        return $this->previous;
    }

    /**
     * @inheritDoc
     */
    public function request(): ?RequestInterface
    {
        return $this->request;
    }

    /**
     * @inheritDoc
     */
    public function response(): ?ResponseInterface
    {
        return $this->response;
    }
}
