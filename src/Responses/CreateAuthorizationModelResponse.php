<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};

use function is_array;

final class CreateAuthorizationModelResponse implements CreateAuthorizationModelResponseInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private string $authorizationModelId,
    ) {
    }

    public function getAuthorizationModelId(): string
    {
        return $this->authorizationModelId;
    }

    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response, SchemaValidator $validator): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            throw new ApiUnexpectedResponseException($exception->getMessage());
        }

        if (201 === $response->getStatusCode() && is_array($data)) {
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
                new SchemaProperty(name: 'authorization_model_id', type: 'string', required: true),
            ],
        );
    }
}
