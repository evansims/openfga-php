<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use InvalidArgumentException;
use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Models\{AuthorizationModel, Metadata, Node, RelationMetadata, TypeDefinition, Userset};
use OpenFGA\Models\Collections\{AuthorizationModels, AuthorizationModelsInterface, Conditions, Nodes, TypeDefinitionRelations, TypeDefinitions, Usersets};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response containing a paginated list of authorization models.
 *
 * This response provides access to authorization models within a store, including
 * pagination support for handling large numbers of models. Each model includes
 * its ID, schema version, and complete type definitions.
 *
 * @see ListAuthorizationModelsResponseInterface For the complete API specification
 */
final class ListAuthorizationModelsResponse extends Response implements ListAuthorizationModelsResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * Create a new list authorization models response instance.
     *
     * @param AuthorizationModelsInterface $models            The collection of authorization models for the current page
     * @param ?string                      $continuationToken Pagination token for fetching additional results, or null if no more pages exist
     */
    public function __construct(
        private readonly AuthorizationModelsInterface $models,
        private readonly ?string $continuationToken = null,
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
    ): ListAuthorizationModelsResponseInterface {
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            // Register all necessary schemas for AuthorizationModel
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
            $validator->registerSchema(AuthorizationModels::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

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
                new SchemaProperty(name: 'authorization_models', type: 'object', className: AuthorizationModels::class, required: true, parameterName: 'models'),
                new SchemaProperty(name: 'continuation_token', type: 'string', required: false),
            ],
        );
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
}
