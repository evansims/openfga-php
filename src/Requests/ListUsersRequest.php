<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\{TupleKeysInterface, UserTypeFiltersInterface};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\{TupleKeyInterface, UserTypeFilterInterface};
use OpenFGA\Network\{RequestContext, RequestMethod};
use Psr\Http\Message\StreamFactoryInterface;

final class ListUsersRequest implements ListUsersRequestInterface
{
    /**
     * @param string                                            $store
     * @param string                                            $authorizationModel
     * @param string                                            $object
     * @param string                                            $relation
     * @param UserTypeFiltersInterface<UserTypeFilterInterface> $userFilters
     * @param ?object                                           $context
     * @param ?TupleKeysInterface<TupleKeyInterface>            $contextualTuples
     * @param ?Consistency                                      $consistency
     */
    public function __construct(
        private string $store,
        private string $authorizationModel,
        private string $object,
        private string $relation,
        private UserTypeFiltersInterface $userFilters,
        private ?object $context = null,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?Consistency $consistency = null,
    ) {
    }

    public function getAuthorizationModel(): string
    {
        return $this->authorizationModel;
    }

    public function getConsistency(): ?Consistency
    {
        return $this->consistency;
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

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = array_filter([
            'authorization_model_id' => $this->authorizationModel,
            'object' => $this->object,
            'relation' => $this->relation,
            'user_filters' => $this->userFilters->jsonSerialize(),
            'context' => $this->context,
            'contextual_tuples' => $this->contextualTuples?->jsonSerialize(),
            'consistency' => $this->consistency?->value,
        ], static fn ($value): bool => null !== $value);

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
