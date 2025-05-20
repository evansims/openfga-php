<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Psr\Http\Message\StreamFactoryInterface;

final class CheckRequest implements CheckRequestInterface
{
    /**
     * @param string                                 $store
     * @param string                                 $authorizationModel
     * @param TupleKeyInterface                      $tupleKey
     * @param ?bool                                  $trace
     * @param ?object                                $context
     * @param ?TupleKeysInterface<TupleKeyInterface> $contextualTuples
     * @param ?Consistency                           $consistency
     */
    public function __construct(
        private string $store,
        private string $authorizationModel,
        private TupleKeyInterface $tupleKey,
        private ?bool $trace = null,
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

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = array_filter([
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'authorization_model_id' => $this->authorizationModel,
            'trace' => $this->trace,
            'context' => $this->context,
            'consistency' => $this->consistency?->value,
            'contextual_tuples' => $this->contextualTuples?->jsonSerialize(),
        ], static fn ($value) => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->getStore() . '/check',
            body: $stream,
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }

    public function getTrace(): ?bool
    {
        return $this->trace;
    }

    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }
}
