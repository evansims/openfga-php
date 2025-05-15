<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\{NetworkRequestMethod, RequestContext};
use OpenFGA\Options\DeleteStoreOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class DeleteStoreRequest implements DeleteStoreRequestInterface
{
    public function __construct(
        private string $store,
        private ?DeleteStoreOptionsInterface $options = null,
    ) {
    }

    public function getOptions(): ?DeleteStoreOptionsInterface
    {
        return $this->options;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: NetworkRequestMethod::DELETE,
            url: '/stores/' . $this->getStore(),
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }
}
