<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{StoreIdInterface, TupleKeyInterface};
use OpenFGA\RequestOptions\ListTuplesOptions;

final class ListTuplesRequest
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private StoreIdInterface $storeId,
        private TupleKeyInterface $tupleKey,
        private ?ListTuplesOptions $options = null,
    ) {
    }

    public function getOptions(): ?ListTuplesOptions
    {
        return $this->options;
    }

    public function getStoreId(): StoreIdInterface
    {
        return $this->storeId;
    }

    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }

    public function toJson(): string
    {
        $body = [];

        $body['tuple_key'] = $this->tupleKey->jsonSerialize();

        if (null !== $this->getOptions()?->getConsistency()) {
            $body['consistency'] = $this->getOptions()?->getConsistency()->value;
        }

        if (null !== $this->getOptions()?->getPageSize()) {
            $body['page_size'] = $this->getOptions()?->getPageSize();
        }

        if (null !== $this->getOptions()?->getContinuationToken()) {
            $body['continuation_token'] = (string) $this->getOptions()?->getContinuationToken();
        }

        return json_encode($body, JSON_THROW_ON_ERROR);
    }

    public function toRequest(): RequestInterface
    {
        $body = $this->requestFactory->getHttpStreamFactory()->createStream($this->toJson());

        return $this->requestFactory->post(
            url: $this->requestFactory->getEndpointUrl('/stores/' . (string) $this->getStoreId() . '/read'),
            options: $this->getOptions(),
            body: $body,
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
