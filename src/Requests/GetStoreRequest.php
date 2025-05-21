<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class GetStoreRequest implements GetStoreRequestInterface
{
    public function __construct(
        private string $store,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/' . $this->store,
        );
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getStore(): string
    {
        return $this->store;
    }
}
