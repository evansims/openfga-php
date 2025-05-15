<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\{NetworkRequestMethod, RequestContext};
use OpenFGA\Options\GetAuthorizationModelOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class GetAuthorizationModelRequest implements GetAuthorizationModelRequestInterface
{
    public function __construct(
        private string $store,
        private string $authorizationModel,
        private ?GetAuthorizationModelOptionsInterface $options = null,
    ) {
    }

    public function getAuthorizationModel(): string
    {
        return $this->authorizationModel;
    }

    public function getOptions(): ?GetAuthorizationModelOptionsInterface
    {
        return $this->options;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: NetworkRequestMethod::GET,
            url: '/stores/' . (string) $this->getStore() . '/authorization-models/' . (string) $this->getAuthorizationModel(),
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }
}
