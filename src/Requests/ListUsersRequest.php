<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\{TupleKeysInterface, UserTypeFiltersInterface};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\{TupleKeyInterface, UserTypeFilterInterface};
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class ListUsersRequest implements ListUsersRequestInterface
{
    /**
     * @param string                                            $store
     * @param string                                            $model
     * @param string                                            $object
     * @param string                                            $relation
     * @param UserTypeFiltersInterface<UserTypeFilterInterface> $userFilters
     * @param ?object                                           $context
     * @param ?TupleKeysInterface<TupleKeyInterface>            $contextualTuples
     * @param ?Consistency                                      $consistency
     */
    public function __construct(
        private string $store,
        private string $model,
        private string $object,
        private string $relation,
        private UserTypeFiltersInterface $userFilters,
        private ?object $context = null,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?Consistency $consistency = null,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getConsistency(): ?Consistency
    {
        return $this->consistency;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getContext(): ?object
    {
        return $this->context;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getContextualTuples(): ?TupleKeysInterface
    {
        return $this->contextualTuples;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getModel(): string
    {
        return $this->model;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getObject(): string
    {
        return $this->object;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getRelation(): string
    {
        return $this->relation;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = array_filter([
            'authorization_model_id' => $this->model,
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
            url: '/stores/' . $this->store . '/list-users',
            body: $stream,
        );
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getStore(): string
    {
        return $this->store;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getUserFilters(): UserTypeFiltersInterface
    {
        return $this->userFilters;
    }
}
