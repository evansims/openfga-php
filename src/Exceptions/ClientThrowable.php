<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

interface ClientThrowable extends Throwable
{
    /**
     * @return array<string, mixed>
     */
    public function context(): array;

    public function kind(): ClientError | AuthenticationError | ConfigurationError | NetworkError | SerializationError;

    public function previous(): ?Throwable;

    public function request(): ?RequestInterface;

    public function response(): ?ResponseInterface;
}
