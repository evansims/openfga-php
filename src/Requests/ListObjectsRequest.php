<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

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

    #[Override]
    public function getAuthorizationModel(): ?string
    {
        return $this->authorizationModel;
    }

    #[Override]
    public function getConsistency(): ?Consistency
    {
        return $this->consistency;
    }

    #[Override]
    public function getContext(): ?object
    {
        return $this->context;
    }

    #[Override]
    public function getContextualTuples(): ?TupleKeysInterface
    {
        return $this->contextualTuples;
    }

    #[Override]
    public function getRelation(): string
    {
        return $this->relation;
    }

    #[Override]
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
        ], static fn ($value): bool => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->getStore() . '/list-objects',
            body: $stream,
        );
    }

    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }

    #[Override]
    public function getType(): string
    {
        return $this->type;
    }

    #[Override]
    public function getUser(): string
    {
        return $this->user;
    }
}
