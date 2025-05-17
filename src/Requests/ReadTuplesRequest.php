<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestMethod, RequestContext};
use OpenFGA\Options\ReadTuplesOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class ReadTuplesRequest implements ReadTuplesRequestInterface
{
    public function __construct(
        private string $store,
        private TupleKeyInterface $tupleKey,
        private ?ReadTuplesOptionsInterface $options = null,
    ) {
    }

    public function getOptions(): ?ReadTuplesOptionsInterface
    {
        return $this->options;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = [
            'tuple_key' => $this->tupleKey->jsonSerialize(),
        ];

        if (null !== $this->getOptions()?->getConsistency()) {
            $body['consistency'] = $this->getOptions()->getConsistency()->value;
        }

        if (null !== $this->getOptions()?->getPageSize()) {
            $body['page_size'] = $this->getOptions()->getPageSize();
        }

        if (null !== $this->getOptions()?->getContinuationToken()) {
            $body['continuation_token'] = (string) $this->getOptions()->getContinuationToken();
        }

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->getStore() . '/read',
            body: $stream,
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }

    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }
}
