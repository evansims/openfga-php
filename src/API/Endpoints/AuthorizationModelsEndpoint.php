<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints;

use OpenFGA\API\Models\{ReadAuthorizationModelResponse, ReadAuthorizationModelsResponse, WriteAuthorizationModelRequest, WriteAuthorizationModelResponse};
use OpenFGA\API\Options\{GetAuthorizationModelsOptions, GetAuthorizationModelOptions, CreateAuthorizationModelOptions};
use OpenFGA\API\Request;

trait AuthorizationModelsEndpoint
{
    final public function getAuthorizationModels(?string $storeId = null, ?GetAuthorizationModelsOptions $options = null): ReadAuthorizationModelsResponse
    {
        $storeId ??= $this->getConfiguration()->getStoreId();

        if (null === $options) {
            $options = new GetAuthorizationModelsOptions();
        }

        $request = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/authorization-models',
        );

        $response = $request->get();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get authorization models');
        }

        $json = $request->getResponseBodyJson();

        return new ReadAuthorizationModelsResponse($json);
    }

    final public function getAuthorizationModel(?string $id = null, ?string $storeId = null, ?GetAuthorizationModelOptions $options = null): ReadAuthorizationModelResponse
    {
        $storeId ??= $this->getConfiguration()->getStoreId();
        $id ??= $this->getConfiguration()->getAuthorizationModelId();

        if (null === $options) {
            $options = new GetAuthorizationModelOptions();
        }

        $request = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/authorization-models/' . $id,
        );

        $response = $request->get();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get authorization models');
        }

        $json = $request->getResponseBodyJson();

        return new ReadAuthorizationModelResponse($json);
    }

    final public function createAuthorizationModel(WriteAuthorizationModelRequest $request, ?string $storeId = null, ?CreateAuthorizationModelOptions $options = null): WriteAuthorizationModelResponse
    {
        $storeId ??= $this->getConfiguration()->getStoreId();

        if (null === $options) {
            $options = new CreateAuthorizationModelOptions();
        }

        $request = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/authorization-models',
        );

        $response = $request->post();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get authorization models');
        }

        $json = $request->getResponseBodyJson();

        return new WriteAuthorizationModelResponse($json);
    }
}
