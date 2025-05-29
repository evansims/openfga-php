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
        if ('' === $this->store) {
            throw new InvalidArgumentException('Store ID cannot be empty');
        }

        if ('' === $this->model) {
            throw new InvalidArgumentException('Authorization Model ID cannot be empty');
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getAuthorizationModel(): string
    {
        return $this->model;
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
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $contextualTuples = $this->contextualTuples?->jsonSerialize();

        $body = array_filter([
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'authorization_model_id' => $this->model,
            'trace' => $this->trace,
            'context' => $this->context,
            'consistency' => $this->consistency?->value,
            'contextual_tuples' => $contextualTuples,
        ], static fn ($value): bool => null !== $value && (! is_array($value) || [] !== $value));

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/check',
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
    public function getTrace(): ?bool
    {
        return $this->trace;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }
}
