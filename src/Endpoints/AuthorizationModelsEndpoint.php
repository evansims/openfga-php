<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use OpenFGA\RequestOptions\{GetAuthorizationModelsOptions, GetAuthorizationModelOptions, CreateAuthorizationModelOptions};
use OpenFGA\Requests\Request;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

trait AuthorizationModelsEndpoint
{
    public ?RequestInterface $lastRequest = null;
    public ?ResponseInterface $lastResponse = null;

    final public function getAuthorizationModels(
        ?string $storeId = null,
        ?GetAuthorizationModelsOptions $options = null,
    ): ReadAuthorizationModelsResponse
    {
        $options ??= new GetAuthorizationModelsOptions();
        $storeId = $this->getStoreId($storeId);

        $request = $this->getRequestFactory()->get(
            url: $this->getRequestFactory()->getEndpointUrl('/stores/' . $storeId . '/authorization-models'),
            options: $options,
            headers: $this->getRequestFactory()->getEndpointHeaders(),
        );

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ReadAuthorizationModelsResponse::fromResponse($this->lastResponse);
    }

    final public function getAuthorizationModel(
        ?string $storeId = null,
        ?string $id = null,
        ?GetAuthorizationModelOptions $options = null,
    ): ReadAuthorizationModelResponse
    {
        $options ??= new GetAuthorizationModelOptions();
        $storeId = $this->getStoreId($storeId);
        $id = $this->getAuthorizationModelId($id);

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/authorization-models/' . $id,
        );

        $this->lastRequest = $api->getRequest();

        $response = $api->get();

        $this->lastResponse = $response;

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get authorization model');
        }

        $json = $api->getResponseBodyJson();

        return new ReadAuthorizationModelResponse($json);
    }

    final public function createAuthorizationModel(
        WriteAuthorizationModelRequest $request,
        ?string $storeId = null,
        ?CreateAuthorizationModelOptions $options = null,
    ): WriteAuthorizationModelResponse
    {
        $storeId = $this->getStoreId($storeId);

        if (null === $options) {
            $options = new CreateAuthorizationModelOptions();
        }

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/authorization-models',
        );

        $this->lastRequest = $api->getRequest();

        $response = $api->post();

        $this->lastResponse = $response;

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get authorization models');
        }

        $json = $api->getResponseBodyJson();

        return new WriteAuthorizationModelResponse($json);
    }
}
