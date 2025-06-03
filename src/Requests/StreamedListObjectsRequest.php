<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use const JSON_THROW_ON_ERROR;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionException;

use function is_array;

/**
 * Request for streaming objects that a user has a specific relationship with.
 *
 * This request finds all objects of a given type where the specified user has
 * the requested relationship, returning results as a stream for efficient processing
 * of large datasets. It's useful for building resource lists, dashboards,
 * or any interface that shows what a user can access when dealing with thousands
 * of objects.
 *
 * @see StreamedListObjectsRequestInterface For the complete API specification
 * @see https://openfga.dev/api/service#/Relationship%20Queries/StreamedListObjects Streamed list objects API endpoint
 */
final readonly class StreamedListObjectsRequest implements StreamedListObjectsRequestInterface
{
    /**
     * @param string                                 $store            The store ID
     * @param string                                 $type             The object type
     * @param string                                 $relation         The relation
     * @param string                                 $user             The user
     * @param ?string                                $model            Authorization model ID (optional)
     * @param ?object                                $context          Context object (optional)
     * @param ?TupleKeysInterface<TupleKeyInterface> $contextualTuples Contextual tuples (optional)
     * @param ?Consistency                           $consistency      Consistency requirement (optional)
     *
     * @throws ClientThrowable          If the store ID, type, relation, user, or model ID (when provided) is empty
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $store,
        private string $type,
        private string $relation,
        private string $user,
        private ?string $model = null,
        private ?object $context = null,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?Consistency $consistency = null,
    ) {
        if ('' === $this->store) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_ID_EMPTY)]);
        }

        if ('' === $this->type) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_TYPE_EMPTY)]);
        }

        if ('' === $this->relation) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_RELATION_EMPTY)]);
        }

        if ('' === $this->user) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_USER_EMPTY)]);
        }

        if (null !== $this->model && '' === $this->model) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_MODEL_ID_EMPTY)]);
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
    public function getModel(): ?string
    {
        return $this->model;
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
        $contextualTuples = $this->contextualTuples?->jsonSerialize();

        $body = array_filter([
            'type' => $this->type,
            'relation' => $this->relation,
            'user' => $this->user,
            'authorization_model_id' => $this->model,
            'context' => $this->context,
            'contextual_tuples' => $contextualTuples,
            'consistency' => $this->consistency?->value,
        ], static fn ($value): bool => null !== $value && (! is_array($value) || [] !== $value));

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/streamed-list-objects',
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getUser(): string
    {
        return $this->user;
    }
}
