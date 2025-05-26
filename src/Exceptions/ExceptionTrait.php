<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

trait ExceptionTrait
{
    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return $this->context;
    }

    public function kind(): ClientError | AuthenticationError | ConfigurationError | NetworkError | SerializationError
    {
        return $this->kind;
    }

    public function request(): ?RequestInterface
    {
        return $this->request;
    }

    public function response(): ?ResponseInterface
    {
        return $this->response;
    }
}
