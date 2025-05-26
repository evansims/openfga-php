<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\SchemaValidator;
use Override;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class WriteTuplesResponse extends Response implements WriteTuplesResponseInterface
{
    #[Override]
    /**
     * @inheritDoc
     */
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): WriteTuplesResponseInterface {
        // Handle successful responses
        if (204 === $response->getStatusCode()) {
            return new self();
        }

        // Handle network errors
        RequestManager::handleResponseException(
            response: $response,
            request: $request,
        );
    }
}
