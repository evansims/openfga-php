<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Psr\Http\Message\StreamFactoryInterface;

final class ExpandRequest implements ExpandRequestInterface
{
    /**
     * @param string                                 $store
     * @param TupleKeyInterface                      $tupleKey
     * @param ?string                                $authorizationModel
     * @param ?TupleKeysInterface<TupleKeyInterface> $contextualTuples
     * @param ?Consistency                           $consistency
     */
    public function __construct(
        private string $store,
        private TupleKeyInterface $tupleKey,
        private ?string $authorizationModel = null,
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

    public function getContextualTuples(): ?TupleKeysInterface
    {
        return $this->contextualTuples;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = array_filter([
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'authorization_model_id' => $this->authorizationModel,
            'consistency' => $this->consistency?->value,
            'contextual_tuples' => $this->contextualTuples?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->getStore() . '/expand',
            body: $stream,
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }

    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }
}
