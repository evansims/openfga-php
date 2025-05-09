<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\{ApiEndpointException, ApiForbiddenException, ApiInternalServerException, ApiTimeoutException, ApiTransactionException, ApiUnuthenticatedException, ApiValidationException};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

abstract class Response implements ResponseInterface
{
    /**
     * @return array<string, mixed>
     */
    final public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    abstract public function toArray(): array;

    abstract public static function fromArray(array $data): static;

    abstract public static function fromResponse(HttpResponseInterface $response): static;

    final public static function handleResponseException(HttpResponseInterface $response): void
    {
        $error = '';

        try {
            $error = trim((string) $response->getBody());
        } catch (Exception) {
        }

        if ('' === $error) {
            $error = 'Unknown error';
        }

        $error = json_encode($error, JSON_THROW_ON_ERROR);

        if (400 === $response->getStatusCode()) {
            throw new ApiValidationException($error);
        }

        if (401 === $response->getStatusCode()) {
            throw new ApiUnuthenticatedException($error);
        }

        if (403 === $response->getStatusCode()) {
            throw new ApiForbiddenException($error);
        }

        if (404 === $response->getStatusCode()) {
            throw new ApiEndpointException($error);
        }

        if (409 === $response->getStatusCode()) {
            throw new ApiTransactionException($error);
        }

        if (422 === $response->getStatusCode()) {
            throw new ApiTimeoutException($error);
        }

        if (500 === $response->getStatusCode()) {
            throw new ApiInternalServerException($error);
        }
    }
}
