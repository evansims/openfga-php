<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{AuthorizationModelId, AuthorizationModelIdInterface, ConsistencyPreference, ContextualTupleKeys, ContextualTupleKeysInterface, StoreId, StoreIdInterface, UserTypeFilters};
use OpenFGA\RequestOptions\ListUsersOptions;

final class ListUsersRequest
{
    public function __construct(
        private RequestFactory $requestFactory,
        private string $object,
        private string $relation,
        private UserTypeFilters $userFilters,
        private ?object $context = null,
        private ?ContextualTupleKeysInterface $contextualTuples = null,
        private ?ConsistencyPreference $consistency = null,
        private ?StoreIdInterface $storeId = null,
        private ?AuthorizationModelIdInterface $authorizationModelId = null,
        private ?ListUsersOptions $options = null,
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

    public function getObject(): string
    {
        return $this->object;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getStoreId(): ?StoreId
    {
        return $this->storeId;
    }

    public function getUserFilters(): UserTypeFilters
    {
        return $this->userFilters;
    }

    public function toJson(): string
    {
        $body = [];

        $body['object'] = $this->object;
        $body['relation'] = $this->relation;
        $body['user_filters'] = $this->userFilters->toArray();

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
            url: $this->requestFactory->getEndpointUrl('/stores/' . $this->storeId . '/list-users'),
            options: $this->options,
            body: $body,
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
