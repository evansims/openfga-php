<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints;

use OpenFGA\API\Endpoints\Traits\AuthorizationModelIdentifiers;
use OpenFGA\API\Endpoints\Traits\StoreIdentifiers;
use OpenFGA\API\Models\ReadAuthorizationModelResponse;
use OpenFGA\API\Options\GetAuthorizationModelOptions;
use OpenFGA\API\Request;
use OpenFGA\ClientInterface;

final class ModelEndpoint
{
    use StoreIdentifiers, AuthorizationModelIdentifiers;

    public function __construct(
        private ClientInterface $client,
        private ?string $modelId = null,
        private ?string $storeId = null,
    ) {
    }

    final public function get(?string $modelId = null, ?string $storeId = null, ?GetAuthorizationModelOptions $options = null): ReadAuthorizationModelResponse
    {
        $storeId ??= $this->getStoreId();
        $modelId ??= $this->getModelId();

        if (null === $options) {
            $options = new GetAuthorizationModelOptions();
        }

        $api = new Request(
            client: $this->client,
            options: $options,
            endpoint: '/stores/' . $storeId . '/authorization-models/' . $modelId,
        );

        $response = $api->get();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get authorization model');
        }

        $json = $api->getResponseBodyJson();

        return new ReadAuthorizationModelResponse($json);
    }
}
