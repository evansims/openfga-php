<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
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

    #[Override]
    public function getAuthorizationModel(): string
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
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = array_filter([
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'authorization_model_id' => $this->authorizationModel,
            'trace' => $this->trace,
            'context' => $this->context,
            'consistency' => $this->consistency?->value,
            'contextual_tuples' => $this->contextualTuples?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->getStore() . '/check',
            body: $stream,
        );
    }

    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }

    #[Override]
    public function getTrace(): ?bool
    {
        return $this->trace;
    }

    #[Override]
    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }
}
