<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{AuthorizationModelIdInterface, ConsistencyPreference, ContextualTupleKeysInterface, StoreIdInterface, TupleKeyInterface};
use OpenFGA\RequestOptions\CheckOptions;

use function assert;

final class CheckRequest
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private TupleKeyInterface $tupleKey,
        private ?bool $trace,
        private ?object $context,
        private ?ContextualTupleKeysInterface $contextualTuples,
        private ?ConsistencyPreference $consistency,
        private ?StoreIdInterface $storeId,
        private ?AuthorizationModelIdInterface $authorizationModelId,
        private CheckOptions $options,
    ) {
    }

    public function getAuthorizationModelId(): ?AuthorizationModelIdInterface
    {
        return $this->authorizationModelId;
    }

    public function getConsistency(): ?ConsistencyPreference
    {
        return $this->consistency;
    }

    public function getContext(): ?object
    {
        return $this->context;
    }

    public function getContextualTuples(): ?ContextualTupleKeysInterface
    {
        return $this->contextualTuples;
    }

    public function getStoreId(): ?StoreIdInterface
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
        $tupleKey = $this->tupleKey->toArray();

        assert(isset($tupleKey['user'], $tupleKey['relation'], $tupleKey['object']));

        $body['tuple_key'] = [
            'user' => $tupleKey['user'],
            'relation' => $tupleKey['relation'],
            'object' => $tupleKey['object'],
        ];

        if (null !== $this->trace) {
            $body['trace'] = $this->trace;
        }

        if (null !== $this->context) {
            $body['context'] = $this->context;
        }

        if (null !== $this->contextualTuples) {
            $body['contextual_tuples'] = $this->contextualTuples->toArray();
        }

        if (null !== $this->consistency) {
            $body['consistency_preference'] = $this->consistency->value;
        }

        if (null !== $this->authorizationModelId) {
            $body['authorization_model_id'] = (string) $this->authorizationModelId;
        }

        return json_encode($body, JSON_THROW_ON_ERROR);
    }

    public function toRequest(): RequestInterface
    {
        $body = $this->requestFactory->getHttpStreamFactory()->createStream($this->toJson());

        return $this->requestFactory->post(
            url: $this->requestFactory->getEndpointUrl('/stores/' . $this->storeId . '/check'),
            options: $this->options,
            body: $body,
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
