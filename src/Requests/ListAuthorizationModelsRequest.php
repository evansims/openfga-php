<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\{NetworkRequestMethod, RequestContext};
use OpenFGA\Options\ListAuthorizationModelsOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class ListAuthorizationModelsRequest implements ListAuthorizationModelsRequestInterface
{
    public function __construct(
        private string $store,
        private ?ListAuthorizationModelsOptionsInterface $options = null,
    ) {
    }

    public function getOptions(): ?ListAuthorizationModelsOptionsInterface
    {
        return $this->options;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: NetworkRequestMethod::GET,
            url: '/stores/' . (string) $this->getStore() . '/authorization-models',
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }
}
