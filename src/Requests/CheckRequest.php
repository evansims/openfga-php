<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{AuthorizationModelIdInterface, ContextualTupleKeysInterface, StoreIdInterface, TupleKeyInterface};
use OpenFGA\RequestOptions\CheckOptions;

use function assert;

final class CheckRequest
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private StoreIdInterface $storeId,
        private AuthorizationModelIdInterface $authorizationModelId,
        private TupleKeyInterface $tupleKey,
        private ?bool $trace = null,
        private ?object $context = null,
        private ?ContextualTupleKeysInterface $contextualTuples = null,
        private ?CheckOptions $options = null,
    ) {
    }

    public function getAuthorizationModelId(): AuthorizationModelIdInterface
    {
        return $this->authorizationModelId;
    }

    public function getContext(): ?object
    {
        return $this->context;
    }

    public function getContextualTuples(): ?ContextualTupleKeysInterface
    {
        return $this->contextualTuples;
    }

    public function getOptions(): ?CheckOptions
    {
        return $this->options;
    }

    public function getStoreId(): StoreIdInterface
    {
        return $this->storeId;
    }

    public function getTrace(): ?bool
    {
        return $this->trace;
    }

    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }

    public function toJson(): string
    {
        $body = [];
        $tupleKey = $this->getTupleKey()->toArray();

        assert(isset($tupleKey['user'], $tupleKey['relation'], $tupleKey['object']));

        $body['tuple_key'] = [
            'user' => $tupleKey['user'],
            'relation' => $tupleKey['relation'],
            'object' => $tupleKey['object'],
        ];

        if (null !== $this->getContextualTuples()) {
            $body['contextual_tuples'] = $this->getContextualTuples()->toArray();
        }

        if (null !== $this->getAuthorizationModelId()) {
            $body['authorization_model_id'] = (string) $this->getAuthorizationModelId();
        }

        if (null !== $this->getTrace()) {
            $body['trace'] = $this->getTrace();
        }

        if (null !== $this->getContext()) {
            $body['context'] = $this->getContext();
        }

        if (null !== $this->getOptions()?->getConsistency()) {
            $body['consistency'] = (string) $this->getOptions()?->getConsistency();
        }

        return json_encode($body, JSON_THROW_ON_ERROR);
    }

    public function toRequest(): RequestInterface
    {
        $body = $this->requestFactory->getHttpStreamFactory()->createStream($this->toJson());

        return $this->requestFactory->post(
            url: $this->requestFactory->getEndpointUrl('/stores/' . (string) $this->storeId . '/check'),
            options: $this->getOptions(),
            body: $body,
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
