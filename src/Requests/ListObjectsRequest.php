<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\TupleKeysInterface;
use OpenFGA\Network\{RequestMethod, RequestContext};
use OpenFGA\Options\ListObjectsOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class ListObjectsRequest implements ListObjectsRequestInterface
{
    public function __construct(
        private string $store,
        private string $type,
        private string $relation,
        private string $user,
        private ?string $authorizationModel = null,
        private ?object $context = null,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?ListObjectsOptionsInterface $options = null,
    ) {
    }

    public function getAuthorizationModel(): ?string
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

    public function getOptions(): ?ListObjectsOptionsInterface
    {
        return $this->options;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = [];

        $body['type'] = $this->getType();
        $body['relation'] = $this->getRelation();
        $body['user'] = $this->getUser();

        if (null !== $this->getAuthorizationModel()) {
            $body['authorization_model_id'] = $this->getAuthorizationModel();
        }

        if (null !== $this->getContextualTuples()) {
            $body['contextual_tuples'] = $this->getContextualTuples()->jsonSerialize();
        }

        if (null !== $this->getContext()) {
            $body['context'] = $this->getContext();
        }

        if (null !== $this->getOptions()?->getConsistency()) {
            $body['consistency'] = (string) $this->getOptions()->getConsistency()->value;
        }

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . (string) $this->getStore() . '/list-objects',
            body: $stream,
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUser(): string
    {
        return $this->user;
    }
}
