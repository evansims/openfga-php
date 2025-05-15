<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\AssertionsInterface;
use OpenFGA\Network\{NetworkRequestMethod, RequestContext};
use OpenFGA\Options\WriteAssertionsOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class WriteAssertionsRequest implements WriteAssertionsRequestInterface
{
    public function __construct(
        private AssertionsInterface $assertions,
        private string $store,
        private string $authorizationModel,
        private ?WriteAssertionsOptionsInterface $options = null,
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

    public function getOptions(): ?WriteAssertionsOptionsInterface
    {
        return $this->options;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = ['assertions' => $this->assertions->jsonSerialize()];

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: NetworkRequestMethod::PUT,
            url: '/stores/' . $this->getStore() . '/assertions/' . $this->getAuthorizationModel(),
            body: $stream,
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }
}
