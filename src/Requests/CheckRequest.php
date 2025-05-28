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

use function assert;

final class CheckRequest implements CheckRequestInterface
{
    /**
     * @param string                                 $store
     * @param string                                 $model
     * @param TupleKeyInterface                      $tupleKey
     * @param ?bool                                  $trace
     * @param ?object                                $context
     * @param ?TupleKeysInterface<TupleKeyInterface> $contextualTuples
     * @param ?Consistency                           $consistency
     */
    public function __construct(
        private string $store,
        private string $model,
        private TupleKeyInterface $tupleKey,
        private ?bool $trace = null,
        private ?object $context = null,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?Consistency $consistency = null,
    ) {
        assert('' !== $this->store, new InvalidArgumentException('Store ID cannot be empty'));
        assert('' !== $this->model, new InvalidArgumentException('Authorization Model ID cannot be empty'));
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getAuthorizationModel(): string
    {
        return $this->model;
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
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = array_filter([
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'authorization_model_id' => $this->model,
            'trace' => $this->trace,
            'context' => $this->context,
            'consistency' => $this->consistency?->value,
            'contextual_tuples' => $this->contextualTuples?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/check',
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
    public function getTrace(): ?bool
    {
        return $this->trace;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }
}
