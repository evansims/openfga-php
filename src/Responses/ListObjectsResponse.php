<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use InvalidArgumentException;
use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty, SchemaValidatorInterface};
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response containing a list of objects that a user has a specific relationship with.
 *
 * This response provides an array of object identifiers that the specified user
 * has the given relationship with. Use this to discover what resources a user
 * can access in your authorization system.
 *
 * @see ListObjectsResponseInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/ListObjects
 */
final class ListObjectsResponse extends Response implements ListObjectsResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * Create a new list objects response instance.
     *
     * @param array<int, string> $objects The array of object identifiers returned by the list operation
     */
    public function __construct(
        private readonly array $objects,
    ) {
    }

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
        SchemaValidatorInterface $validator,
    ): ListObjectsResponseInterface {
        // Handle successful responses
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        // Handle network errors
        RequestManager::handleResponseException(
            response: $response,
            request: $request,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'objects', type: 'array', items: ['type' => 'string'], required: true),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getObjects(): array
    {
        return $this->objects;
    }
}
