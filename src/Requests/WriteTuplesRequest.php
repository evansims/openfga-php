<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\TupleKeysInterface;
use OpenFGA\Network\{RequestMethod, RequestContext};
use OpenFGA\Options\WriteTuplesOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class WriteTuplesRequest implements WriteTuplesRequestInterface
{
    public function __construct(
        private string $store,
        private string $authorizationModel,
        private ?TupleKeysInterface $writes = null,
        private ?TupleKeysInterface $deletes = null,
        private ?WriteTuplesOptionsInterface $options = null,
    ) {
    }

    public function getAuthorizationModel(): string
    {
        return $this->authorizationModel;
    }

    public function getDeletes(): ?TupleKeysInterface
    {
        return $this->deletes;
    }

    public function getOptions(): ?WriteTuplesOptionsInterface
    {
        return $this->options;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = [
            'authorization_model_id' => $this->getAuthorizationModel(),
        ];

        if (null !== $this->getWrites()) {
            $body['writes'] = $this->getWrites()->jsonSerialize();
        }

        if (null !== $this->getDeletes()) {
            $body['deletes'] = $this->getDeletes()->jsonSerialize();
        }

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->getStore() . '/write',
            body: $stream,
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }

    public function getWrites(): ?TupleKeysInterface
    {
        return $this->writes;
    }
}
