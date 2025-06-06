<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use InvalidArgumentException;
use OpenFGA\Network\RequestManager;
use OpenFGA\Schemas\SchemaValidator;
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response confirming successful writing of test assertions.
 *
 * This response indicates that test assertions have been successfully stored
 * for an authorization model. The assertions can now be used to validate
 * that the model behaves correctly in various authorization scenarios.
 *
 * @see WriteAssertionsResponseInterface For the complete API specification
 */
final class WriteAssertionsResponse extends Response implements WriteAssertionsResponseInterface
{
    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public static function fromResponse(
        HttpResponseInterface $response,
        HttpRequestInterface $request,
        SchemaValidator $validator,
    ): WriteAssertionsResponseInterface {
        if (204 === $response->getStatusCode()) {
            return new self;
        }

        RequestManager::handleResponseException(
            response: $response,
            request: $request,
        );
    }
}
