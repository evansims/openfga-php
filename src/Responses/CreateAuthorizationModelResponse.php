<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;

final class CreateAuthorizationModelResponse implements CreateAuthorizationModelResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private string $authorizationModelId,
    ) {
    }

    public function getAuthorizationModelId(): string
    {
        return $this->authorizationModelId;
    }

    public static function fromResponse(HttpResponseInterface $response, SchemaValidator $validator): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (201 === $response->getStatusCode() && is_array($data)) {
            $validator->registerSchema(self::Schema());

            return $validator->validateAndTransform($data, self::class);
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }

    public static function Schema(): SchemaInterface
    {
        return new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'authorization_model_id', type: 'string', required: true),
            ],
        );
    }
}
