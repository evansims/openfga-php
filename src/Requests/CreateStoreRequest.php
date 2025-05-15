<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\{NetworkRequestMethod, RequestContext};
use OpenFGA\Options\CreateStoreOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class CreateStoreRequest implements CreateStoreRequestInterface
{
    public function __construct(
        private string $name,
        private ?CreateStoreOptionsInterface $options = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOptions(): ?CreateStoreOptionsInterface
    {
        return $this->options;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = [
            'name' => $this->getName(),
        ];

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: NetworkRequestMethod::POST,
            url: '/stores/',
            body: $stream,
        );
    }
}
