<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Schema\SchemaValidator;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

final class DeleteStoreResponse implements DeleteStoreResponseInterface
{
    use ResponseTrait;

    public function __construct(
    ) {
    }

    public static function fromResponse(HttpResponseInterface $response, SchemaValidator $validator): static
    {
        if (204 === $response->getStatusCode()) {
            return new self();
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException('');
    }
}
