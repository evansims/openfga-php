<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\{NetworkRequestMethod, RequestContext};
use OpenFGA\Options\GetStoreOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class GetStoreRequest implements GetStoreRequestInterface
{
    public function __construct(
        private string $store,
        private ?GetStoreOptionsInterface $options = null,
    ) {
    }

    public function getOptions(): ?GetStoreOptionsInterface
    {
        return $this->options;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: NetworkRequestMethod::GET,
            url: '/stores/' . (string) $this->getStore(),
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }
}
