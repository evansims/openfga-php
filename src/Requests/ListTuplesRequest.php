<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{AuthorizationModelId, ConsistencyPreference, StoreId, TupleKeyInterface};
use OpenFGA\RequestOptions\ListTuplesOptions;

final class ListTuplesRequest
{
    public function __construct(
        private RequestFactory $requestFactory,
        private TupleKeyInterface $tupleKey,
        private ?ConsistencyPreference $consistency,
        private ?StoreId $storeId,
        private ?AuthorizationModelId $authorizationModelId,
        private ListTuplesOptions $options,
    ) {
    }

    public function getAuthorizationModelId(): ?AuthorizationModelId
    {
        return $this->authorizationModelId;
    }

    public function getStoreId(): ?StoreId
    {
        return $this->storeId;
    }

    public function toJson(): string
    {
        $body = [];

        $body['tuple_key'] = $this->tupleKey->toArray();

        if (null !== $this->consistency) {
            $body['consistency'] = $this->consistency->value;
        }

        if (null !== $this->authorizationModelId) {
            $body['authorization_model_id'] = (string) $this->authorizationModelId;
        }

        if (null !== $this->options->getPageSize()) {
            $body['page_size'] = $this->options->getPageSize();
        }

        if (null !== $this->options->getContinuationToken()) {
            $body['continuation_token'] = $this->options->getContinuationToken();
        }

        return json_encode($body, JSON_THROW_ON_ERROR);
    }

    public function toRequest(): Request
    {
        $body = $this->requestFactory->getHttpStreamFactory()->createStream($this->toJson());

        return $this->requestFactory->post(
            url: $this->requestFactory->getEndpointUrl('/stores/' . $this->storeId . '/read'),
            options: $this->options,
            body: $body,
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
