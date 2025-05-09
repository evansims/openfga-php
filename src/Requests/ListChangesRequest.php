<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use DateTimeImmutable;
use OpenFGA\Models\{AuthorizationModelId, StoreId};
use OpenFGA\RequestOptions\ListChangesOptions;

final class ListChangesRequest
{
    public function __construct(
        private RequestFactory $requestFactory,
        private ?string $type,
        private ?DateTimeImmutable $startTime,
        private ?StoreId $storeId,
        private ?AuthorizationModelId $authorizationModelId,
        private ListChangesOptions $options,
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

    public function getType(): string
    {
        return $this->type;
    }

    public function toJson(): string
    {
        $body = [];

        $body['type'] = $this->type;

        if (null !== $this->startTime) {
            $body['start_time'] = $this->startTime->format('Y-m-d\TH:i:s.vP');
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

        if (null !== $this->authorizationModelId) {
            $body['authorization_model_id'] = (string) $this->authorizationModelId;
        }

        return json_encode($body, JSON_THROW_ON_ERROR);
    }

    public function toRequest(): Request
    {
        $body = $this->requestFactory->getHttpStreamFactory()->createStream($this->toJson());

        return $this->requestFactory->post(
            url: $this->requestFactory->getEndpointUrl('/stores/' . $this->storeId . '/list-objects'),
            options: $this->options,
            body: $body,
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
