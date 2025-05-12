<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use OpenFGA\Models\{AuthorizationModelId, StoreId};

use OpenFGA\Models\{AuthorizationModelIdInterface, ConditionsInterface, SchemaVersion, StoreIdInterface, TypeDefinitionsInterface};
use OpenFGA\RequestOptions\{CreateModelOptions, GetModelOptions, ListModelsOptions};
use OpenFGA\Requests\{CreateModelRequest, GetModelRequest, ListModelsRequest};
use OpenFGA\Responses\{CreateModelResponse, GetModelResponse, ListModelsResponse};
use Psr\Http\Message\{RequestInterface, ResponseInterface};

use function is_string;

trait ModelsEndpoint
{
    public ?RequestInterface $lastRequest = null;

    public ?ResponseInterface $lastResponse = null;

    /**
     * Creates a new authorization model.
     *
     * This function sends a POST request to the /stores/{store_id}/authorization-models endpoint
     * to create a new authorization model. It returns a CreateAuthorizationModelResponse object.
     *
     * @param StoreIdInterface|string  $storeId         The store ID.
     * @param TypeDefinitionsInterface $typeDefinitions The type definitions for the authorization model.
     * @param ConditionsInterface      $conditions      The conditions for the authorization model.
     * @param SchemaVersion            $schemaVersion   The schema version of the authorization model. Defaults to "1.1".
     * @param null|CreateModelOptions  $options         Optional request options.
     *
     * @return CreateModelResponse The response indicating the write outcome.
     */
    final public function createModel(
        StoreIdInterface | string $storeId,
        TypeDefinitionsInterface $typeDefinitions,
        ConditionsInterface $conditions,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
        ?CreateModelOptions $options = null,
    ): CreateModelResponse {
        $options ??= new CreateModelOptions();
        $storeId = is_string($storeId) ? StoreId::fromString($storeId) : $storeId;

        $request = (new CreateModelRequest(
            requestFactory: $this->getRequestFactory(),
            typeDefinitions: $typeDefinitions,
            conditions: $conditions,
            schemaVersion: $schemaVersion,
            storeId: $storeId,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return CreateModelResponse::fromResponse($this->lastResponse);
    }

    /**
     * Retrieves an authorization model.
     *
     * This function sends a GET request to the /stores/{store_id}/authorization-models/{authorization_model_id} endpoint
     * to retrieve an authorization model. It returns a GetModelResponse object.
     *
     * @param StoreIdInterface|string              $storeId              The store ID.
     * @param AuthorizationModelIdInterface|string $authorizationModelId The authorization model ID.
     * @param null|GetModelOptions                 $options              Optional request options.
     *
     * @return GetModelResponse The response containing the authorization model.
     */
    final public function getModel(
        StoreIdInterface | string $storeId,
        AuthorizationModelIdInterface | string $authorizationModelId,
        ?GetModelOptions $options = null,
    ): GetModelResponse {
        $options ??= new GetModelOptions();
        $storeId = is_string($storeId) ? StoreId::fromString($storeId) : $storeId;
        $authorizationModelId = is_string($authorizationModelId) ? AuthorizationModelId::fromString($authorizationModelId) : $authorizationModelId;

        $request = (new GetModelRequest(
            requestFactory: $this->getRequestFactory(),
            storeId: $storeId,
            authorizationModelId: $authorizationModelId,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return GetModelResponse::fromResponse($this->lastResponse);
    }

    /**
     * Lists authorization models.
     *
     * This function sends a GET request to the /stores/{store_id}/authorization-models endpoint
     * to retrieve authorization models. It returns a ListModelsResponse object.
     *
     * @param StoreIdInterface|string $storeId The store ID.
     * @param null|ListModelsOptions  $options Optional request options.
     *
     * @return ListModelsResponse The response containing the authorization models.
     */
    final public function listModels(
        StoreIdInterface | string $storeId,
        ?ListModelsOptions $options = null,
    ): ListModelsResponse {
        $options ??= new ListModelsOptions();
        $storeId = is_string($storeId) ? StoreId::fromString($storeId) : $storeId;

        $request = (new ListModelsRequest(
            requestFactory: $this->getRequestFactory(),
            storeId: $storeId,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ListModelsResponse::fromResponse($this->lastResponse);
    }
}
