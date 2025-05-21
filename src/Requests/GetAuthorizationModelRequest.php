<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class GetAuthorizationModelRequest implements GetAuthorizationModelRequestInterface
{
    public function __construct(
        private string $store,
        private string $authorizationModel,
    ) {
    }

    #[Override]
    public function getAuthorizationModel(): string
    {
        return $this->authorizationModel;
    }

    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/' . $this->getStore() . '/authorization-models/' . $this->getAuthorizationModel(),
        );
    }

    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }
}
