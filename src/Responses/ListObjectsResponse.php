<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class ListObjectsResponse extends Response implements ListObjectsResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * @param array<int, string> $objects
     */
    public function __construct(
        private array $objects,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getObjects(): array
    {
        return $this->objects;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
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

    #[Override]
    /**
     * @inheritDoc
     */
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'objects', type: 'array', items: ['type' => 'string'], required: true),
            ],
        );
    }
}
