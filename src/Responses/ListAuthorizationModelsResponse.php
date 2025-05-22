<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\AuthorizationModelInterface;
use OpenFGA\Models\Collections\{AuthorizationModels, AuthorizationModelsInterface};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};

use Override;

use function is_array;

final class ListAuthorizationModelsResponse implements ListAuthorizationModelsResponseInterface
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
    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response, SchemaValidator $validator): ListAuthorizationModelsResponseInterface
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            throw new ApiUnexpectedResponseException($exception->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data)) {
            $validator->registerSchema(AuthorizationModels::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        RequestManager::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
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
