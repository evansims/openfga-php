<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{TupleKeyInterface, TupleKeysInterface};
use OpenFGA\Network\{NetworkRequestMethod, RequestContext};
use OpenFGA\Options\ExpandOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class ExpandRequest implements ExpandRequestInterface
{
    public function __construct(
        private string $store,
        private TupleKeyInterface $tupleKey,
        private ?string $authorizationModel = null,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?ExpandOptionsInterface $options = null,
    ) {
    }

    public function getAuthorizationModel(): ?string
    {
        return $this->authorizationModel;
    }

    public function getContextualTuples(): ?TupleKeysInterface
    {
        return $this->contextualTuples;
    }

    public function getOptions(): ?ExpandOptionsInterface
    {
        return $this->options;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = [];
        $tupleKey = $this->getTupleKey()->jsonSerialize();

        $body['tuple_key'] = [
            'relation' => $tupleKey['relation'],
            'object' => $tupleKey['object'],
        ];

        if (null !== $this->getAuthorizationModel()) {
            $body['authorization_model_id'] = $this->getAuthorizationModel();
        }

        if (null !== $this->getOptions()?->getConsistency()) {
            $body['consistency'] = (string) $this->getOptions()->getConsistency()->value;
        }

        if (null !== $this->getContextualTuples()) {
            $body['contextual_tuples'] = $this->getContextualTuples()->jsonSerialize();
        }

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: NetworkRequestMethod::POST,
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
