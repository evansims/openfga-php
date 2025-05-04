<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Exceptions\{ApiEndpointException, ApiForbiddenException, ApiInternalServerException, ApiTimeoutException, ApiTransactionException, ApiUnauthenticatedException, ApiValidationException};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use Exception;

abstract class Response implements ResponseInterface
{
    abstract public function toArray(): array;

    abstract public static function fromArray(array $data): static;

    abstract public static function fromResponse(HttpResponseInterface $response): static;

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function handleResponseException(HttpResponseInterface $response): void
    {
        $error = '';

        try {
            $error = trim((string) $response->getBody());
        } catch (Exception) {
        }

        if ($error === '') {
            $error = 'Unknown error';
        }

        $error = json_encode($error, JSON_THROW_ON_ERROR);

        if ($response->getStatusCode() === 400) {
            throw new ApiValidationException($error);
        }

        if ($response->getStatusCode() === 401) {
            throw new ApiUnauthenticatedException($error);
        }

        if ($response->getStatusCode() === 403) {
            throw new ApiForbiddenException($error);
        }

        if ($response->getStatusCode() === 404) {
            throw new ApiEndpointException($error);
        }

        if ($response->getStatusCode() === 409) {
            throw new ApiTransactionException($error);
        }

        if ($response->getStatusCode() === 422) {
            throw new ApiTimeoutException($error);
        }

        if ($response->getStatusCode() === 500) {
            throw new ApiInternalServerException($error);
        }
    }
}
