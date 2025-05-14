<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{AuthorizationModelIdInterface, StoreIdInterface, TupleKeysInterface};
use OpenFGA\RequestOptions\WriteTuplesOptions;

final class WriteTuplesRequest
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private StoreIdInterface $storeId,
        private AuthorizationModelIdInterface $authorizationModelId,
        private ?TupleKeysInterface $writes = null,
        private ?TupleKeysInterface $deletes = null,
        private ?WriteTuplesOptions $options = null,
    ) {
    }

    public function getAuthorizationModelId(): AuthorizationModelIdInterface
    {
        return $this->authorizationModelId;
    }

    public function getDeletes(): ?TupleKeysInterface
    {
        return $this->deletes;
    }

    public function getOptions(): ?WriteTuplesOptions
    {
        return $this->options;
    }

    public function getStoreId(): StoreIdInterface
    {
        return $this->storeId;
    }

    public function getWrites(): ?TupleKeysInterface
    {
        return $this->writes;
    }

    public function toJson(): string
    {
        $body = [];

        if (null !== $this->getWrites()) {
            $body['writes'] = $this->getWrites()->jsonSerialize();
        }

        if (null !== $this->getDeletes()) {
            $body['deletes'] = $this->getDeletes()->jsonSerialize();
        }

        $body['authorization_model_id'] = (string) $this->getAuthorizationModelId();

        return json_encode($body, JSON_THROW_ON_ERROR);
    }

    public function toRequest(): RequestInterface
    {
        $body = $this->requestFactory->getHttpStreamFactory()->createStream($this->toJson());

        return $this->requestFactory->post(
            url: $this->requestFactory->getEndpointUrl('/stores/' . (string) $this->getStoreId() . '/write'),
            options: $this->getOptions(),
            body: $body,
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
