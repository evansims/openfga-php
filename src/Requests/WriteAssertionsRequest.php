<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\AssertionInterface;
use OpenFGA\Models\Collections\AssertionsInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class WriteAssertionsRequest implements WriteAssertionsRequestInterface
{
    /**
     * @param AssertionsInterface<AssertionInterface> $assertions
     * @param string                                  $store
     * @param string                                  $model
     */
    public function __construct(
        private AssertionsInterface $assertions,
        private string $store,
        private string $model,
    ) {
    }

    #[Override]
    public function getAssertions(): AssertionsInterface
    {
        return $this->assertions;
    }

    #[Override]
    public function getModel(): string
    {
        return $this->model;
    }

    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = ['assertions' => $this->assertions->jsonSerialize()];

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::PUT,
            url: '/stores/' . $this->store . '/assertions/' . $this->model,
            body: $stream,
        );
    }

    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }
}
