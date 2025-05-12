<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{Assertions, AuthorizationModelIdInterface, StoreIdInterface};
use OpenFGA\RequestOptions\WriteAssertionsOptions;

final class WriteAssertionsRequest
{
    public function __construct(
        private RequestFactory $requestFactory,
        private Assertions $assertions,
        private StoreIdInterface $storeId,
        private AuthorizationModelIdInterface $authorizationModelId,
        private ?WriteAssertionsOptions $options = null,
    ) {
    }

    public function getAssertions(): Assertions
    {
        return $this->assertions;
    }

    public function getAuthorizationModelId(): AuthorizationModelIdInterface
    {
        return $this->authorizationModelId;
    }

    public function getOptions(): ?WriteAssertionsOptions
    {
        return $this->options;
    }

    public function getStoreId(): StoreIdInterface
    {
        return $this->storeId;
    }

    public function toJson(): string
    {
        $body = [];

        $body['assertions'] = $this->assertions->toArray();

        return json_encode($body, JSON_THROW_ON_ERROR);
    }

    public function toRequest(): Request
    {
        $body = $this->requestFactory->getHttpStreamFactory()->createStream($this->toJson());

        return $this->requestFactory->put(
            url: $this->requestFactory->getEndpointUrl('/stores/' . (string) $this->getStoreId() . '/assertions/' . (string) $this->getAuthorizationModelId()),
            options: $this->getOptions(),
            body: $body,
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
