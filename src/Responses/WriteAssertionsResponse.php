<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\SchemaValidator;
use Override;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class WriteAssertionsResponse extends Response implements WriteAssertionsResponseInterface
{
    /**
     * @inheritDoc
     */
    #[Override]
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): WriteAssertionsResponseInterface {
        // Handle successful responses
        if (204 === $response->getStatusCode()) {
            return new self();
        }

        // Handle network errors
        return RequestManager::handleResponseException(
            response: $response,
            request: $request,
        );
    }
}
