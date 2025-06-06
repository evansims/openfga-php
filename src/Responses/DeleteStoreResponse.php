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
 * Response confirming successful deletion of a store.
 *
 * This response is returned when a store has been successfully deleted from the
 * OpenFGA service. The response contains no additional data as the store has been
 * permanently removed.
 *
 * @see DeleteStoreResponseInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Stores/DeleteStore
 */
final class DeleteStoreResponse implements DeleteStoreResponseInterface
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
    ): DeleteStoreResponseInterface {
        if (204 === $response->getStatusCode()) {
            return new self;
        }

        RequestManager::handleResponseException(
            response: $response,
            request: $request,
        );
    }
}
