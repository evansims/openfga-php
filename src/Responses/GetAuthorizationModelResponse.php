<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, Collections\Conditions, Collections\Nodes, Collections\TypeDefinitionRelations, Collections\TypeDefinitions, Collections\Usersets, Metadata, Node, RelationMetadata, TypeDefinition, Userset};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class GetAuthorizationModelResponse extends Response implements GetAuthorizationModelResponseInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private ?AuthorizationModelInterface $model = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getModel(): ?AuthorizationModelInterface
    {
        return $this->model;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
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
                new SchemaProperty(name: 'authorization_model', type: 'object', className: AuthorizationModel::class, required: false, parameterName: 'model'),
            ],
        );
    }
}
