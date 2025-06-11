<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use InvalidArgumentException;
use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schemas\{SchemaInterface, SchemaProperty, SchemaValidatorInterface};
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response confirming successful creation of a new authorization model.
 *
 * This response provides the unique identifier of the newly created authorization
 * model, which can be used for subsequent operations like checks, expansions,
 * and model management activities.
 *
 * @see CreateAuthorizationModelResponseInterface For the complete API specification
 */
final class CreateAuthorizationModelResponse extends Response implements CreateAuthorizationModelResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * Create a new create authorization model response instance.
     *
     * @param string $model The unique identifier of the created authorization model
     */
    public function __construct(
        private readonly string $model,
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws NetworkException         If the API returns an error response
     * @throws ReflectionException      If exception location capture fails
     * @throws SerializationException   If JSON parsing or schema validation fails
     */
    #[Override]
    public static function fromResponse(
        HttpResponseInterface $response,
        HttpRequestInterface $request,
        SchemaValidatorInterface $validator,
    ): CreateAuthorizationModelResponseInterface {
        if (201 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        // Handle network errors
        RequestManager::handleResponseException(
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
                new SchemaProperty(name: 'authorization_model_id', type: 'string', required: true, parameterName: 'model'),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getModel(): string
    {
        return $this->model;
    }
}
