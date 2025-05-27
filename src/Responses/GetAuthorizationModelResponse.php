<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class GetAuthorizationModelResponse extends Response implements GetAuthorizationModelResponseInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private ?AuthorizationModelInterface $model = null,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getModel(): ?AuthorizationModelInterface
    {
        return $this->model;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): GetAuthorizationModelResponseInterface {
        // Handle successful responses
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(AuthorizationModel::schema());
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
                new SchemaProperty(name: 'authorization_model', type: AuthorizationModel::class, required: false),
            ],
        );
    }
}
