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
    public function getModel(): ?string
    {
        return $this->model;
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
            'type' => $this->type,
            'relation' => $this->relation,
            'user' => $this->user,
            'authorization_model_id' => $this->model,
            'context' => $this->context,
            'contextual_tuples' => $this->contextualTuples?->jsonSerialize(),
            'consistency' => $this->consistency?->value,
        ], static fn ($value): bool => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/list-objects',
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
    public function getType(): string
    {
        return $this->type;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getUser(): string
    {
        return $this->user;
    }
}
