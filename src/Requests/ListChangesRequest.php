<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\StoreIdInterface;
use OpenFGA\RequestOptions\ListChangesOptions;

final class ListChangesRequest
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private StoreIdInterface $storeId,
        private ?ListChangesOptions $options = null,
    ) {
    }

    public function getOptions(): ?ListChangesOptions
    {
        return $this->options;
    }

    public function getStoreId(): StoreIdInterface
    {
        return $this->storeId;
    }

    public function toRequest(): RequestInterface
    {
        return $this->requestFactory->get(
            url: $this->requestFactory->getEndpointUrl('/stores/' . (string) $this->getStoreId() . '/changes'),
            options: $this->getOptions(),
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
