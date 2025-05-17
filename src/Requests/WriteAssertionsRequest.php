<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\AssertionsInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Psr\Http\Message\StreamFactoryInterface;

final class WriteAssertionsRequest implements WriteAssertionsRequestInterface
{
    public function __construct(
        private AssertionsInterface $assertions,
        private string $store,
        private string $authorizationModel,
    ) {
    }

    public function getAssertions(): AssertionsInterface
    {
        return $this->assertions;
    }

    public function getAuthorizationModel(): string
    {
        return $this->authorizationModel;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = ['assertions' => $this->assertions->jsonSerialize()];

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::PUT,
            url: '/stores/' . $this->getStore() . '/assertions/' . $this->getAuthorizationModel(),
            body: $stream,
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }
}
