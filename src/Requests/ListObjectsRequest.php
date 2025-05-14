<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{AuthorizationModelIdInterface, StoreIdInterface, TupleKeysInterface};
use OpenFGA\RequestOptions\ListObjectsOptions;

final class ListObjectsRequest
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private StoreIdInterface $storeId,
        private string $type,
        private string $relation,
        private string $user,
        private ?AuthorizationModelIdInterface $authorizationModelId = null,
        private ?object $context = null,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?ListObjectsOptions $options = null,
    ) {
    }

    public function getAuthorizationModelId(): ?AuthorizationModelIdInterface
    {
        return $this->authorizationModelId;
    }

    public function getContext(): ?object
    {
        return $this->context;
    }

    public function getContextualTuples(): ?TupleKeysInterface
    {
        return $this->contextualTuples;
    }

    public function getOptions(): ?ListObjectsOptions
    {
        return $this->options;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getStoreId(): StoreIdInterface
    {
        return $this->storeId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function toJson(): string
    {
        $body = [];

        $body['type'] = $this->getType();
        $body['relation'] = $this->getRelation();
        $body['user'] = $this->getUser();

        if (null !== $this->getAuthorizationModelId()) {
            $body['authorization_model_id'] = (string) $this->getAuthorizationModelId();
        }

        if (null !== $this->getContextualTuples()) {
            $body['contextual_tuples'] = $this->getContextualTuples()->jsonSerialize();
        }

        if (null !== $this->getContext()) {
            $body['context'] = $this->getContext();
        }

        if (null !== $this->getOptions()?->getConsistency()) {
            $body['consistency'] = (string) $this->getOptions()?->getConsistency();
        }

        return json_encode($body, JSON_THROW_ON_ERROR);
    }

    public function toRequest(): Request
    {
        $body = $this->requestFactory->getHttpStreamFactory()->createStream($this->toJson());

        return $this->requestFactory->post(
            url: $this->requestFactory->getEndpointUrl('/stores/' . (string) $this->getStoreId() . '/list-objects'),
            options: $this->getOptions(),
            body: $body,
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
