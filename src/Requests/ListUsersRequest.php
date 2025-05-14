<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{AuthorizationModelIdInterface, StoreIdInterface, TupleKeysInterface, UserTypeFiltersInterface};
use OpenFGA\RequestOptions\ListUsersOptions;

final class ListUsersRequest
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private StoreIdInterface $storeId,
        private AuthorizationModelIdInterface $authorizationModelId,
        private string $object,
        private string $relation,
        private UserTypeFiltersInterface $userFilters,
        private ?object $context = null,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?ListUsersOptions $options = null,
    ) {
    }

    public function getAuthorizationModelId(): ?AuthorizationModelIdInterface
    {
        return $this->authorizationModelId;
    }

    public function getContext(): ?object
    {
        return $this->context;
    }

    public function getContextualTuples(): ?TupleKeysInterface
    {
        return $this->contextualTuples;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function getOptions(): ?ListUsersOptions
    {
        return $this->options;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getStoreId(): StoreIdInterface
    {
        return $this->storeId;
    }

    public function getUserFilters(): UserTypeFiltersInterface
    {
        return $this->userFilters;
    }

    public function toJson(): string
    {
        $body = [];

        $body['authorization_model_id'] = (string) $this->getAuthorizationModelId();
        $body['object'] = $this->getObject();
        $body['relation'] = $this->getRelation();
        $body['user_filters'] = $this->getUserFilters()->jsonSerialize();

        if (null !== $this->getContextualTuples()) {
            $body['contextual_tuples'] = $this->getContextualTuples()->jsonSerialize();
        }

        if (null !== $this->getContext()) {
            $body['context'] = $this->getContext();
        }

        if (null !== $this->getOptions()?->getConsistency()) {
            $body['consistency'] = (string) $this->getOptions()?->getConsistency();
        }

        return json_encode($body, JSON_THROW_ON_ERROR);
    }

    public function toRequest(): RequestInterface
    {
        $body = $this->requestFactory->getHttpStreamFactory()->createStream($this->toJson());

        return $this->requestFactory->post(
            url: $this->requestFactory->getEndpointUrl('/stores/' . (string) $this->getStoreId() . '/list-users'),
            options: $this->getOptions(),
            body: $body,
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
