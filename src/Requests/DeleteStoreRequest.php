<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use InvalidArgumentException;

final class DeleteStoreRequest implements DeleteStoreRequestInterface
{
    public function __construct(
        private string $store,
    ) {
        assert($this->store !== '', new InvalidArgumentException('Store ID cannot be empty'));
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: RequestMethod::DELETE,
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
