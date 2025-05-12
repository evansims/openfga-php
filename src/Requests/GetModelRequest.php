<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{AuthorizationModelIdInterface, StoreIdInterface};
use OpenFGA\RequestOptions\GetModelOptions;

final class GetModelRequest
{
    public function __construct(
        private RequestFactory $requestFactory,
        private StoreIdInterface $storeId,
        private AuthorizationModelIdInterface $authorizationModelId,
        private ?GetModelOptions $options = null,
    ) {
    }

    public function getAuthorizationModelId(): AuthorizationModelIdInterface
    {
        return $this->authorizationModelId;
    }

    public function getOptions(): ?GetModelOptions
    {
        return $this->options;
    }

    public function getStoreId(): StoreIdInterface
    {
        return $this->storeId;
    }

    public function toRequest(): Request
    {
        return $this->requestFactory->get(
            url: $this->requestFactory->getEndpointUrl('/stores/' . (string) $this->getStoreId() . '/authorization-models/' . (string) $this->getAuthorizationModelId()),
            options: $this->getOptions(),
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
