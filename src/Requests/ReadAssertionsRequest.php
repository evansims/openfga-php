<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use InvalidArgumentException;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class ReadAssertionsRequest implements ReadAssertionsRequestInterface
{
    public function __construct(
        private string $store,
        private string $model,
    ) {
        if ('' === $this->store) {
            throw new InvalidArgumentException('Store ID cannot be empty');
        }

        if ('' === $this->model) {
            throw new InvalidArgumentException('Authorization model ID cannot be empty');
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/' . $this->store . '/assertions/' . $this->model,
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
