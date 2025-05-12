<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\RequestOptions\CreateStoreRequestOptions;

final class CreateStoreRequest
{
    public function __construct(
        private RequestFactory $requestFactory,
        private string $name,
        private ?CreateStoreRequestOptions $options = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOptions(): ?CreateStoreRequestOptions
    {
        return $this->options;
    }

    public function toJson(): string
    {
        $body = [];

        $body['name'] = $this->name;

        return json_encode($body, JSON_THROW_ON_ERROR);
    }

    public function toRequest(): Request
    {
        $body = $this->requestFactory->getHttpStreamFactory()->createStream($this->toJson());

        return $this->requestFactory->post(
            url: $this->requestFactory->getEndpointUrl('/stores'),
            options: $this->getOptions(),
            body: $body,
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
