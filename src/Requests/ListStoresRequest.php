<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\{NetworkRequestMethod, RequestContext};
use OpenFGA\Options\ListStoresOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class ListStoresRequest implements ListStoresRequestInterface
{
    public function __construct(
        private ?ListStoresOptionsInterface $options = null,
    ) {
    }

    public function getOptions(): ?ListStoresOptionsInterface
    {
        return $this->options;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: NetworkRequestMethod::GET,
            url: '/stores',
        );
    }
}
