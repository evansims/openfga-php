<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\{RequestContext, RequestMethod};
use Psr\Http\Message\StreamFactoryInterface;

final class ReadAssertionsRequest implements ReadAssertionsRequestInterface
{
    public function __construct(
        private string $store,
        private string $authorizationModel,
    ) {
    }

    public function getAuthorizationModel(): string
    {
        return $this->authorizationModel;
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
