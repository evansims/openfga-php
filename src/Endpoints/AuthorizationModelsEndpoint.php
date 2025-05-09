<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use OpenFGA\Models\{ConditionsInterface, TypeDefinitionsInterface};
use OpenFGA\RequestOptions\{CreateAuthorizationModelOptions, GetAuthorizationModelOptions, ListAuthorizationModelsOptions};
use OpenFGA\Responses\{CreateAuthorizationModelResponse, GetAuthorizationModelResponse, ListAuthorizationModelsResponse};
use Psr\Http\Message\{RequestInterface, ResponseInterface};

trait AuthorizationModelsEndpoint
{
    public ?RequestInterface $lastRequest = null;

    public ?ResponseInterface $lastResponse = null;

    /**
     * Creates a new authorization model.
     *
     * This function sends a POST request to the /stores/{store_id}/authorization-models endpoint
     * to create a new authorization model. It returns a CreateAuthorizationModelResponse object.
     *
     * @param TypeDefinitionsInterface             $typeDefinitions The type definitions for the authorization model.
     * @param ConditionsInterface                  $conditions      The conditions for the authorization model.
     * @param string                               $schemaVersion   The schema version of the authorization model. Defaults to "1.1".
     * @param null|string                          $storeId         The store ID. Uses the default store ID if null.
     * @param null|CreateAuthorizationModelOptions $options         Optional request options.
     *
     * @return CreateAuthorizationModelResponse The response indicating the write outcome.
     */
    final public function createAuthorizationModel(
        TypeDefinitionsInterface $typeDefinitions,
        ConditionsInterface $conditions,
        string $schemaVersion = '1.1',
        ?string $storeId = null,
        ?CreateAuthorizationModelOptions $options = null,
    ): CreateAuthorizationModelResponse {
        $options ??= new CreateAuthorizationModelOptions();
        $storeId = $this->getStoreId($storeId);

        $jsonBody = json_encode([
            'type_definitions' => $typeDefinitions->toArray(),
            'conditions' => $conditions->toArray(),
            'schema_version' => $schemaVersion,
        ]);

        // Ensure we have a valid JSON string
        if (false === $jsonBody) {
            $jsonBody = '{"type_definitions": [], "conditions": {}, "schema_version": "' . $schemaVersion . '"}';
        }

        $body = $this->getRequestFactory()->getHttpStreamFactory()->createStream($jsonBody);

        $request = $this->getRequestFactory()->post(
            url: $this->getRequestFactory()->getEndpointUrl('/stores/' . $storeId . '/authorization-models'),
            options: $options,
            body: $body,
            headers: $this->getRequestFactory()->getEndpointHeaders(),
        );

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return CreateAuthorizationModelResponse::fromResponse($this->lastResponse);
    }

    /**
     * Retrieves an authorization model.
     *
     * This function sends a GET request to the /stores/{store_id}/authorization-models/{authorization_model_id} endpoint
     * to retrieve an authorization model. It returns a GetAuthorizationModelResponse object.
     *
     * @param null|string                       $storeId The store ID. Uses the default store ID if null.
     * @param null|string                       $id      The authorization model ID. Uses the default authorization model ID if null.
     * @param null|GetAuthorizationModelOptions $options Optional request options.
     *
     * @return GetAuthorizationModelResponse The response containing the authorization model.
     */
    final public function getAuthorizationModel(
        ?string $storeId = null,
        ?string $id = null,
        ?GetAuthorizationModelOptions $options = null,
    ): GetAuthorizationModelResponse {
        $options ??= new GetAuthorizationModelOptions();
        $storeId = $this->getStoreId($storeId);
        $id = $this->getAuthorizationModelId($id);

        $request = $this->getRequestFactory()->get(
            url: $this->getRequestFactory()->getEndpointUrl('/stores/' . $storeId . '/authorization-models/' . $id),
            options: $options,
            headers: $this->getRequestFactory()->getEndpointHeaders(),
        );

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return GetAuthorizationModelResponse::fromResponse($this->lastResponse);
    }

    /**
     * Lists authorization models.
     *
     * This function sends a GET request to the /stores/{store_id}/authorization-models endpoint
     * to retrieve authorization models. It returns a ListAuthorizationModelsResponse object.
     *
     * @param null|string                         $storeId The store ID. Uses the default store ID if null.
     * @param null|ListAuthorizationModelsOptions $options Optional request options.
     *
     * @return ListAuthorizationModelsResponse The response containing the authorization models.
     */
    final public function listAuthorizationModels(
        ?string $storeId = null,
        ?ListAuthorizationModelsOptions $options = null,
    ): ListAuthorizationModelsResponse {
        $options ??= new ListAuthorizationModelsOptions();
        $storeId = $this->getStoreId($storeId);

        $request = $this->getRequestFactory()->get(
            url: $this->getRequestFactory()->getEndpointUrl('/stores/' . $storeId . '/authorization-models'),
            options: $options,
            headers: $this->getRequestFactory()->getEndpointHeaders(),
        );

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ListAuthorizationModelsResponse::fromResponse($this->lastResponse);
    }
}
