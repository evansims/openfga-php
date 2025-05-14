<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\RequestOptions\ListStoresRequestOptions;

final class ListStoresRequest
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private ?ListStoresRequestOptions $options = null,
    ) {
    }

    public function getOptions(): ?ListStoresRequestOptions
    {
        return $this->options;
    }

    public function toRequest(): RequestInterface
    {
        return $this->requestFactory->get(
            url: $this->requestFactory->getEndpointUrl('/stores'),
            options: $this->getOptions(),
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
