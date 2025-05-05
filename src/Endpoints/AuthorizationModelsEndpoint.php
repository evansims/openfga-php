<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use OpenFGA\RequestOptions\{ListAuthorizationModelsOptions, GetAuthorizationModelOptions, CreateAuthorizationModelOptions};
use OpenFGA\Responses\{ListAuthorizationModelsResponse, GetAuthorizationModelResponse, CreateAuthorizationModelResponse};
use OpenFGA\Models\{TypeDefinitionsInterface, ConditionsInterface};
use Psr\Http\Message\{RequestInterface, ResponseInterface};

trait AuthorizationModelsEndpoint
{
    public ?RequestInterface $lastRequest = null;
    public ?ResponseInterface $lastResponse = null;

    final public function listAuthorizationModels(
        ?string $storeId = null,
        ?ListAuthorizationModelsOptions $options = null,
    ): ListAuthorizationModelsResponse
    {
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

    final public function getAuthorizationModel(
        ?string $storeId = null,
        ?string $id = null,
        ?GetAuthorizationModelOptions $options = null,
    ): GetAuthorizationModelResponse
    {
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

    final public function createAuthorizationModel(
        TypeDefinitionsInterface $typeDefinitions,
        ConditionsInterface $conditions,
        string $schemaVersion = '1.1',
        ?string $storeId = null,
        ?CreateAuthorizationModelOptions $options = null,
    ): CreateAuthorizationModelResponse
    {
        $options ??= new CreateAuthorizationModelOptions();
        $storeId = $this->getStoreId($storeId);

        $body = $this->getRequestFactory()->getHttpStreamFactory()->createStream(json_encode([
            'type_definitions' => $typeDefinitions->toArray(),
            'conditions' => $conditions->toArray(),
            'schema_version' => $schemaVersion,
        ]));

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
}
