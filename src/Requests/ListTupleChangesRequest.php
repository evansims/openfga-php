<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\{RequestMethod, RequestContext};
use OpenFGA\Options\ListTupleChangesOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class ListTupleChangesRequest implements ListTupleChangesRequestInterface
{
    public function __construct(
        private string $store,
        private ?ListTupleChangesOptionsInterface $options = null,
    ) {
    }

    public function getOptions(): ?ListTupleChangesOptionsInterface
    {
        return $this->options;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/' . (string) $this->getStore() . '/changes',
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }
}
