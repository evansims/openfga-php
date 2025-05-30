<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use InvalidArgumentException;
use OpenFGA\Models\Collections\{TupleKeysInterface, UserTypeFiltersInterface};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\{TupleKeyInterface, UserTypeFilterInterface};
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

use function count;
use function is_array;

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
        if ('' === $this->store) {
            throw new InvalidArgumentException('Store ID cannot be empty');
        }

        if ('' === $this->model) {
            throw new InvalidArgumentException('Authorization model ID cannot be empty');
        }

        if ('' === $this->object) {
            throw new InvalidArgumentException('Object ID cannot be empty');
        }

        if ('' === $this->relation) {
            throw new InvalidArgumentException('Relation cannot be empty');
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getConsistency(): ?Consistency
    {
        return $this->consistency;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getContext(): ?object
    {
        return $this->context;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getContextualTuples(): ?TupleKeysInterface
    {
        return $this->contextualTuples;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getObject(): string
    {
        return $this->object;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRelation(): string
    {
        return $this->relation;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        // Parse the object string into type and id
        $objectParts = explode(':', $this->object, 2);
        $objectData = 2 === count($objectParts)
            ? ['type' => $objectParts[0], 'id' => $objectParts[1]]
            : $this->object;

        $contextualTuples = $this->contextualTuples?->jsonSerialize()['tuple_keys'] ?? null;

        $body = array_filter([
            'authorization_model_id' => $this->model,
            'object' => $objectData,
            'relation' => $this->relation,
            'user_filters' => $this->userFilters->jsonSerialize(),
            'context' => $this->context,
            'contextual_tuples' => $contextualTuples,
            'consistency' => $this->consistency?->value,
        ], static fn ($value): bool => null !== $value && (! is_array($value) || [] !== $value));

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/list-users',
            body: $stream,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getUserFilters(): UserTypeFiltersInterface
    {
        return $this->userFilters;
    }
}
