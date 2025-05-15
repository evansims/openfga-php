<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{TupleKeyInterface, TupleKeysInterface};
use OpenFGA\Network\{NetworkRequestMethod, RequestContext};
use OpenFGA\Options\CheckOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class CheckRequest implements CheckRequestInterface
{
    public function __construct(
        private string $store,
        private string $authorizationModel,
        private TupleKeyInterface $tupleKey,
        private ?bool $trace = null,
        private ?object $context = null,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?CheckOptionsInterface $options = null,
    ) {
    }

    public function getAuthorizationModel(): string
    {
        return $this->authorizationModel;
    }

    public function getContext(): ?object
    {
        return $this->context;
    }

    public function getContextualTuples(): ?TupleKeysInterface
    {
        return $this->contextualTuples;
    }

    public function getOptions(): ?CheckOptionsInterface
    {
        return $this->options;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = [];
        $tupleKey = $this->getTupleKey()->jsonSerialize();

        $body['tuple_key'] = [
            'user' => $tupleKey['user'],
            'relation' => $tupleKey['relation'],
            'object' => $tupleKey['object'],
        ];

        if (null !== $this->getContextualTuples()) {
            $body['contextual_tuples'] = $this->getContextualTuples()->jsonSerialize();
        }

        if (null !== $this->getAuthorizationModel()) {
            $body['authorization_model_id'] = $this->getAuthorizationModel();
        }

        if (null !== $this->getTrace()) {
            $body['trace'] = $this->getTrace();
        }

        if (null !== $this->getContext()) {
            $body['context'] = $this->getContext();
        }

        if (null !== $this->getOptions()?->getConsistency()) {
            $body['consistency'] = (string) $this->getOptions()->getConsistency()->value;
        }

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: NetworkRequestMethod::POST,
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
