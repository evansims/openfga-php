<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class ReadAssertionsRequest implements ReadAssertionsRequestInterface
{
    public function __construct(
        private string $store,
        private string $model,
    ) {
    }

    #[Override]
    public function getModel(): string
    {
        return $this->model;
    }

    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/' . $this->store . '/assertions/' . $this->model,
        );
    }

    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }
}
