<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{TupleKeysInterface, UserTypeFiltersInterface};
use OpenFGA\Network\{RequestMethod, RequestContext};
use OpenFGA\Options\ListUsersOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class ListUsersRequest implements ListUsersRequestInterface
{
    public function __construct(
        private string $store,
        private string $authorizationModel,
        private string $object,
        private string $relation,
        private UserTypeFiltersInterface $userFilters,
        private ?object $context = null,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?ListUsersOptionsInterface $options = null,
    ) {
    }

    public function getAuthorizationModel(): string
    {
        return $this->authorizationModel;
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

    public function getOptions(): ?ListUsersOptionsInterface
    {
        return $this->options;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = [];

        $body['authorization_model_id'] = $this->getAuthorizationModel();
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

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->getStore() . '/list-users',
            body: $stream,
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }

    public function getUserFilters(): UserTypeFiltersInterface
    {
        return $this->userFilters;
    }
}
