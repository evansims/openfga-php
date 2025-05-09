<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{AuthorizationModelId, ConsistencyPreference, ContextualTupleKeys, StoreId};
use OpenFGA\RequestOptions\ListObjectsOptions;

final class ListObjectsRequest
{
    public function __construct(
        private RequestFactory $requestFactory,
        private string $type,
        private string $relation,
        private string $user,
        private ?object $context,
        private ?ContextualTupleKeys $contextualTuples,
        private ?ConsistencyPreference $consistency,
        private ?StoreId $storeId,
        private ?AuthorizationModelId $authorizationModelId,
        private ListObjectsOptions $options,
    ) {
    }

    public function getAuthorizationModelId(): ?AuthorizationModelId
    {
        return $this->authorizationModelId;
    }

    public function getConsistency(): ?ConsistencyPreference
    {
        return $this->consistency;
    }

    public function getContext(): ?object
    {
        return $this->context;
    }

    public function getContextualTuples(): ?ContextualTupleKeys
    {
        return $this->contextualTuples;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getStoreId(): ?StoreId
    {
        return $this->storeId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function toJson(): string
    {
        $body = [];

        $body['type'] = $this->type;
        $body['relation'] = $this->relation;
        $body['user'] = $this->user;

        if (null !== $this->context) {
            $body['context'] = $this->context;
        }

        if (null !== $this->contextualTuples) {
            $body['contextual_tuples'] = $this->contextualTuples->toArray();
        }

        if (null !== $this->consistency) {
            $body['consistency'] = $this->consistency->value;
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
