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
     * @param AuthorizationModelsInterface<AuthorizationModelInterface> $authorizationModels
     * @param ?string                                                   $continuationToken
     */
    public function __construct(
        private AuthorizationModelsInterface $authorizationModels,
        private ?string $continuationToken = null,
    ) {
    }

    #[Override]
    public function getAuthorizationModels(): AuthorizationModelsInterface
    {
        return $this->authorizationModels;
    }

    #[Override]
    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    #[Override]
    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response, SchemaValidator $validator): static
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
