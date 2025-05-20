<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Psr\Http\Message\StreamFactoryInterface;

final class ListObjectsRequest implements ListObjectsRequestInterface
{
    /**
     * @param string                                 $store
     * @param string                                 $type
     * @param string                                 $relation
     * @param string                                 $user
     * @param ?string                                $authorizationModel
     * @param ?object                                $context
     * @param ?TupleKeysInterface<TupleKeyInterface> $contextualTuples
     * @param ?Consistency                           $consistency
     */
    public function __construct(
        private string $store,
        private string $type,
        private string $relation,
        private string $user,
        private ?string $authorizationModel = null,
        private ?object $context = null,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?Consistency $consistency = null,
    ) {
    }

    public function getAuthorizationModel(): ?string
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

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = array_filter([
            'type' => $this->type,
            'relation' => $this->relation,
            'user' => $this->user,
            'authorization_model_id' => $this->authorizationModel,
            'context' => $this->context,
            'contextual_tuples' => $this->contextualTuples?->jsonSerialize(),
            'consistency' => $this->consistency?->value,
        ], static fn ($value) => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->getStore() . '/list-objects',
            body: $stream,
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUser(): string
    {
        return $this->user;
    }
}
