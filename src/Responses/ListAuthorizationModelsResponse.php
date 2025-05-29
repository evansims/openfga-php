<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\{AuthorizationModel, Collections\AuthorizationModels, Collections\AuthorizationModelsInterface, Collections\Conditions, Collections\TypeDefinitions, TypeDefinition};
use OpenFGA\Models\AuthorizationModelInterface;
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

    /**
     * @inheritDoc
     */
    #[Override]
    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getModels(): AuthorizationModelsInterface
    {
        return $this->models;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): ListAuthorizationModelsResponseInterface {
        // Handle successful responses
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            // Register all necessary schemas for AuthorizationModel
            $validator->registerSchema(TypeDefinition::schema());
            $validator->registerSchema(TypeDefinitions::schema());
            $validator->registerSchema(Conditions::schema());
            $validator->registerSchema(AuthorizationModel::schema());
            $validator->registerSchema(AuthorizationModels::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        // Handle network errors
        return RequestManager::handleResponseException(
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
                new SchemaProperty(name: 'authorization_models', type: 'object', className: AuthorizationModels::class, required: true, parameterName: 'models'),
                new SchemaProperty(name: 'continuation_token', type: 'string', required: false),
            ],
        );
    }
}
