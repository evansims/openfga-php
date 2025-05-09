<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{AuthorizationModelId, ConsistencyPreference, ContextualTupleKeys, StoreId, TupleKey};
use OpenFGA\RequestOptions\ExpandOptions;

use function assert;

final class ExpandRequest
{
    public function __construct(
        private RequestFactory $requestFactory,
        private TupleKey $tupleKey,
        private ?ContextualTupleKeys $contextualTuples,
        private ?ConsistencyPreference $consistency,
        private ?StoreId $storeId,
        private ?AuthorizationModelId $authorizationModelId,
        private ExpandOptions $options,
    ) {
    }

    public function getAuthorizationModelId(): ?AuthorizationModelId
    {
        return $this->authorizationModelId;
    }

    public function getConsistency(): ?ConsistencyPreference
    {
        return $this->consistency;
    }

    public function getContextualTuples(): ?ContextualTupleKeys
    {
        return $this->contextualTuples;
    }

    public function getStoreId(): ?StoreId
    {
        return $this->storeId;
    }

    public function getTupleKey(): TupleKey
    {
        return $this->tupleKey;
    }

    public function toJson(): string
    {
        $body = [];
        $tupleKey = $this->tupleKey->toArray();

        assert(isset($tupleKey['relation'], $tupleKey['object']));

        $body['tuple_key'] = [
            'relation' => $tupleKey['relation'],
            'object' => $tupleKey['object'],
        ];

        if (null !== $this->contextualTuples) {
            $body['contextual_tuples'] = $this->contextualTuples->toArray();
        }

        if (null !== $this->consistency) {
            $body['consistency'] = $this->consistency->value;
        }

        if (null !== $this->authorizationModelId) {
            $body['authorization_model_id'] = (string) $this->authorizationModelId;
        }

        return json_encode($body, JSON_THROW_ON_ERROR);
    }

    public function toRequest(): Request
    {
        $body = $this->requestFactory->getHttpStreamFactory()->createStream($this->toJson());

        return $this->requestFactory->post(
            url: $this->requestFactory->getEndpointUrl('/stores/' . $this->storeId . '/expand'),
            options: $this->options,
            body: $body,
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
