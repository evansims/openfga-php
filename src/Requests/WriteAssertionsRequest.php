<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{AssertionsInterface, AuthorizationModelIdInterface, StoreIdInterface};
use OpenFGA\RequestOptions\WriteAssertionsOptions;

final class WriteAssertionsRequest
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private AssertionsInterface $assertions,
        private StoreIdInterface $storeId,
        private AuthorizationModelIdInterface $authorizationModelId,
        private ?WriteAssertionsOptions $options = null,
    ) {
    }

    public function getAssertions(): AssertionsInterface
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

        $body['assertions'] = $this->assertions->jsonSerialize();

        return json_encode($body, JSON_THROW_ON_ERROR);
    }

    public function toRequest(): RequestInterface
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
