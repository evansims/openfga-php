<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use InvalidArgumentException;
use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\SchemaValidator;
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response confirming successful writing of relationship tuples.
 *
 * This response is returned when relationship tuples have been successfully written
 * to the authorization store. The response contains no additional data as the
 * operations have completed successfully.
 *
 * @see WriteTuplesResponseInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Tuples/Write
 */
final class WriteTuplesResponse extends Response implements WriteTuplesResponseInterface
{
    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws NetworkException         If the API returns an error response
     * @throws ReflectionException      If exception location capture fails
     * @throws SerializationException   If JSON parsing or schema validation fails
     */
    #[Override]
    public static function fromResponse(
        HttpResponseInterface $response,
        HttpRequestInterface $request,
        SchemaValidator $validator,
    ): WriteTuplesResponseInterface {
        if (200 === $response->getStatusCode() || 204 === $response->getStatusCode()) {
            return new self;
        }

        RequestManager::handleResponseException(
            response: $response,
            request: $request,
        );
    }
}
