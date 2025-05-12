<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

final class DeleteStoreResponse implements DeleteStoreResponseInterface
{
    use ResponseTrait;

    public function __construct(
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new self();
    }

    public static function fromResponse(HttpResponseInterface $response): static
    {
        if (204 === $response->getStatusCode()) {
            return new static();
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException('');
    }
}
