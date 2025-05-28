<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\{UsersetTree, UsersetTreeInterface};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class ExpandResponse extends Response implements ExpandResponseInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private ?UsersetTreeInterface $tree = null,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getTree(): ?UsersetTreeInterface
    {
        return $this->tree;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): ExpandResponseInterface {
        // Handle successful responses
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(UsersetTree::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        // Handle network errors
        return RequestManager::handleResponseException(
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
                new SchemaProperty(name: 'tree', type: 'object', className: UsersetTree::class, required: false),
            ],
        );
    }
}
