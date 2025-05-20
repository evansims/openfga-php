<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};

use function is_array;

final class GetAuthorizationModelResponse implements GetAuthorizationModelResponseInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private ?AuthorizationModelInterface $authorizationModel = null,
    ) {
    }

    public function getAuthorizationModel(): ?AuthorizationModelInterface
    {
        return $this->authorizationModel;
    }

    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response, SchemaValidator $validator): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data)) {
            $validator->registerSchema(AuthorizationModel::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        RequestManager::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }

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
