<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\{RequestMethod, RequestContext};
use OpenFGA\Options\ReadAssertionsOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class ReadAssertionsRequest implements ReadAssertionsRequestInterface
{
    public function __construct(
        private string $store,
        private string $authorizationModel,
        private ?ReadAssertionsOptionsInterface $options = null,
    ) {
    }

    public function getAuthorizationModel(): string
    {
        return $this->authorizationModel;
    }

    public function getOptions(): ?ReadAssertionsOptionsInterface
    {
        return $this->options;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/' . $this->getStore() . '/assertions/' . $this->getAuthorizationModel(),
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }
}
