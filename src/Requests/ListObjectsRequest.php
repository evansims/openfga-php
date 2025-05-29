<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use InvalidArgumentException;
use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

use function is_array;

final class ListObjectsRequest implements ListObjectsRequestInterface
{
    /**
     * @param string                                 $store
     * @param string                                 $type
     * @param string                                 $relation
     * @param string                                 $user
     * @param ?string                                $model
     * @param ?object                                $context
     * @param ?TupleKeysInterface<TupleKeyInterface> $contextualTuples
     * @param ?Consistency                           $consistency
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
            throw new InvalidArgumentException('Store ID cannot be empty');
        }
        if ('' === $this->type) {
            throw new InvalidArgumentException('Type cannot be empty');
        }
        if ('' === $this->relation) {
            throw new InvalidArgumentException('Relation cannot be empty');
        }
        if ('' === $this->user) {
            throw new InvalidArgumentException('User cannot be empty');
        }

        if (null !== $this->model && '' === $this->model) {
            throw new InvalidArgumentException('Authorization Model ID cannot be empty');
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
            url: '/stores/' . $this->store . '/list-objects',
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
