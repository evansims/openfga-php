<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\SchemaValidator;
use Override;

final class DeleteStoreResponse implements DeleteStoreResponseInterface
{
    #[Override]
    /**
     * @inheritDoc
     */
    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response, SchemaValidator $validator): DeleteStoreResponseInterface
    {
        if (204 === $response->getStatusCode()) {
            return new self();
        }

        RequestManager::handleResponseException($response);

        throw new ApiUnexpectedResponseException('');
    }
}
