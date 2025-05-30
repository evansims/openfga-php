<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use const JSON_THROW_ON_ERROR;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Models\Collections\{TupleKeysInterface, UserTypeFiltersInterface};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\{TupleKeyInterface, UserTypeFilterInterface};
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionException;

use function count;
use function is_array;

/**
 * Request for listing users who have a specific relationship with an object.
 *
 * This request finds all users (or usersets) that have the specified relationship
 * with a given object, filtered by user type. It's useful for building access
 * management interfaces, member lists, and permission auditing tools.
 *
 * @see ListUsersRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/ListUsers List users API endpoint
 */
final readonly class ListUsersRequest implements ListUsersRequestInterface
{
    /**
     * @param string                                            $store            The store ID
     * @param string                                            $model            Authorization model ID
     * @param string                                            $object           The object
     * @param string                                            $relation         The relation
     * @param UserTypeFiltersInterface<UserTypeFilterInterface> $userFilters      User type filters
     * @param ?object                                           $context          Context object (optional)
     * @param ?TupleKeysInterface<TupleKeyInterface>            $contextualTuples Contextual tuples (optional)
     * @param ?Consistency                                      $consistency      Consistency requirement (optional)
     *
     * @throws ClientThrowable          If the store ID, model ID, object, relation is empty or user filters are empty
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
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
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_ID_EMPTY)]);
        }

        if ('' === $this->model) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_MODEL_ID_EMPTY)]);
        }

        if ('' === $this->object) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_OBJECT_EMPTY)]);
        }

        if ('' === $this->relation) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_RELATION_EMPTY)]);
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
     *
     * @throws JsonException
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        // Parse the object string into type and id
        $objectParts = explode(':', $this->object, 2);
        $objectData = 2 === count($objectParts)
            ? ['type' => $objectParts[0], 'id' => $objectParts[1]]
            : $this->object;

        /** @var mixed $contextualTuples */
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
