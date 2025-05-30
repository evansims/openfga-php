<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use InvalidArgumentException;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class GetStoreRequest implements GetStoreRequestInterface
{
    public function __construct(
        private string $store,
    ) {
        if ('' === $this->store) {
            throw new InvalidArgumentException('Store ID cannot be empty');
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/' . $this->store,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }
}
