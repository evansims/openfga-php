<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use InvalidArgumentException;
use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, Collections\Conditions, Collections\Nodes, Collections\TypeDefinitionRelations, Collections\TypeDefinitions, Collections\Usersets, Metadata, Node, RelationMetadata, TypeDefinition, Userset};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response containing a specific authorization model from the store.
 *
 * This response provides the complete authorization model including type definitions,
 * relationships, and conditions. Use this to retrieve and examine the authorization
 * schema that defines how permissions work in your application.
 *
 * @see GetAuthorizationModelResponseInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Authorization%20Models/ReadAuthorizationModel
 */
final class GetAuthorizationModelResponse extends Response implements GetAuthorizationModelResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * Create a new get authorization model response instance.
     *
     * @param ?AuthorizationModelInterface $model The authorization model retrieved from the API
     */
    public function __construct(
        private readonly ?AuthorizationModelInterface $model = null,
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
        SchemaValidator $validator,
    ): GetAuthorizationModelResponseInterface {
        // Handle successful responses
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            // Register all schemas needed for AuthorizationModel
            $validator->registerSchema(Node::schema());
            $validator->registerSchema(Nodes::schema());
            $validator->registerSchema(Userset::schema());
            $validator->registerSchema(Usersets::schema());
            $validator->registerSchema(RelationMetadata::schema());
            $validator->registerSchema(Metadata::schema());
            $validator->registerSchema(TypeDefinitionRelations::schema());
            $validator->registerSchema(TypeDefinition::schema());
            $validator->registerSchema(TypeDefinitions::schema());
            $validator->registerSchema(Conditions::schema());
            $validator->registerSchema(AuthorizationModel::schema());
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
                new SchemaProperty(name: 'authorization_model', type: 'object', className: AuthorizationModel::class, required: false, parameterName: 'model'),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getModel(): ?AuthorizationModelInterface
    {
        return $this->model;
    }
}
