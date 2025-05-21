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
     * @param string                                  $authorizationModel
     */
    public function __construct(
        private AssertionsInterface $assertions,
        private string $store,
        private string $authorizationModel,
    ) {
    }

    #[Override]
    public function getAssertions(): AssertionsInterface
    {
        return $this->assertions;
    }

    #[Override]
    public function getAuthorizationModel(): string
    {
        return $this->authorizationModel;
    }

    #[Override]
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

    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }
}
