<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use InvalidArgumentException;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class CreateStoreRequest implements CreateStoreRequestInterface
{
    public function __construct(
        private string $name,
    ) {
        if ('' === $this->name) {
            throw new InvalidArgumentException('Store name cannot be empty');
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = [
            'name' => $this->getName(),
        ];

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/',
            body: $stream,
            headers: [
                'Content-Type' => 'application/json',
            ],
        );
    }
}
