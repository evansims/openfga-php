<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{AuthorizationModelIdInterface, ContextualTupleKeys, StoreIdInterface, TupleKey};
use OpenFGA\RequestOptions\ExpandOptions;

use function assert;

final class ExpandRequest
{
    public function __construct(
        private RequestFactory $requestFactory,
        private StoreIdInterface $storeId,
        private TupleKey $tupleKey,
        private ?AuthorizationModelIdInterface $authorizationModelId = null,
        private ?ContextualTupleKeys $contextualTuples = null,
        private ?ExpandOptions $options = null,
    ) {
    }

    public function getAuthorizationModelId(): ?AuthorizationModelIdInterface
    {
        return $this->authorizationModelId;
    }

    public function getContextualTuples(): ?ContextualTupleKeys
    {
        return $this->contextualTuples;
    }

    public function getOptions(): ?ExpandOptions
    {
        return $this->options;
    }

    public function getStoreId(): StoreIdInterface
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
        $tupleKey = $this->getTupleKey()->toArray();

        assert(isset($tupleKey['relation'], $tupleKey['object']));

        $body['tuple_key'] = [
            'relation' => $tupleKey['relation'],
            'object' => $tupleKey['object'],
        ];

        if (null !== $this->getAuthorizationModelId()) {
            $body['authorization_model_id'] = (string) $this->getAuthorizationModelId();
        }

        if (null !== $this->getOptions()?->getConsistency()) {
            $body['consistency'] = (string) $this->getOptions()?->getConsistency();
        }

        if (null !== $this->getContextualTuples()) {
            $body['contextual_tuples'] = $this->getContextualTuples()->toArray();
        }

        return json_encode($body, JSON_THROW_ON_ERROR);
    }

    public function toRequest(): Request
    {
        $body = $this->requestFactory->getHttpStreamFactory()->createStream($this->toJson());

        return $this->requestFactory->post(
            url: $this->requestFactory->getEndpointUrl('/stores/' . (string) $this->getStoreId() . '/expand'),
            options: $this->getOptions(),
            body: $body,
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
