<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\AuthorizationModelInterface;
use OpenFGA\Models\Collections\{AuthorizationModels, AuthorizationModelsInterface};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class ListAuthorizationModelsResponse extends Response implements ListAuthorizationModelsResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * @param AuthorizationModelsInterface<AuthorizationModelInterface> $models
     * @param ?string                                                   $continuationToken
     */
    public function __construct(
        private AuthorizationModelsInterface $models,
        private ?string $continuationToken = null,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getModels(): AuthorizationModelsInterface
    {
        return $this->models;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): ListAuthorizationModelsResponseInterface {
        // Handle successful responses
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(AuthorizationModels::schema());
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
                new SchemaProperty(name: 'authorization_models', type: AuthorizationModels::class, required: true),
                new SchemaProperty(name: 'continuation_token', type: 'string', required: false),
            ],
        );
    }
}
