<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\StoreIdInterface;
use OpenFGA\RequestOptions\DeleteStoreRequestOptions;

final class DeleteStoreRequest
{
    public function __construct(
        private RequestFactory $requestFactory,
        private StoreIdInterface $storeId,
        private ?DeleteStoreRequestOptions $options = null,
    ) {
    }

    public function getOptions(): ?DeleteStoreRequestOptions
    {
        return $this->options;
    }

    public function getStoreId(): StoreIdInterface
    {
        return $this->storeId;
    }

    public function toRequest(): Request
    {
        return $this->requestFactory->delete(
            url: $this->requestFactory->getEndpointUrl('/stores/' . (string) $this->getStoreId()),
            options: $this->getOptions(),
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
