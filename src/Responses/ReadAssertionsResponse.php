<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{Assertions, AssertionsInterface};
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;

final class ReadAssertionsResponse implements ReadAssertionsResponseInterface
{
    private static ?SchemaInterface $schema = null;

    use ResponseTrait;

    public function __construct(
        private ?AssertionsInterface $assertions,
        private string $authorizationModelId,
    ) {
    }

    public function getAssertions(): ?AssertionsInterface
    {
        return $this->assertions;
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

        if (200 === $response->getStatusCode() && is_array($data)) {
            $validator->registerSchema(Assertions::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'assertions', type: Assertions::class, required: false),
                new SchemaProperty(name: 'authorization_model_id', type: 'string', required: true),
            ],
        );
    }
}
