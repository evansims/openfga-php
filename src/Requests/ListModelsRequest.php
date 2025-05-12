<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\StoreIdInterface;
use OpenFGA\RequestOptions\ListModelsOptions;

final class ListModelsRequest
{
    public function __construct(
        private RequestFactory $requestFactory,
        private StoreIdInterface $storeId,
        private ?ListModelsOptions $options = null,
    ) {
    }

    public function getOptions(): ?ListModelsOptions
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
            url: $this->requestFactory->getEndpointUrl('/stores/' . $this->getStoreId() . '/authorization-models'),
            options: $this->getOptions(),
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
