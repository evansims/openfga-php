<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Psr\Http\Message\StreamFactoryInterface;

final class WriteTuplesRequest implements WriteTuplesRequestInterface
{
    /**
     * @param string                                     $store
     * @param string                                     $authorizationModel
     * @param null|TupleKeysInterface<TupleKeyInterface> $writes
     * @param null|TupleKeysInterface<TupleKeyInterface> $deletes
     */
    public function __construct(
        private string $store,
        private string $authorizationModel,
        private ?TupleKeysInterface $writes = null,
        private ?TupleKeysInterface $deletes = null,
    ) {
    }

    public function getAuthorizationModel(): string
    {
        return $this->authorizationModel;
    }

    public function getDeletes(): ?TupleKeysInterface
    {
        return $this->deletes;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = array_filter([
            'authorization_model_id' => $this->getAuthorizationModel(),
            'writes' => $this->getWrites()?->jsonSerialize(),
            'deletes' => $this->getDeletes()?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->getStore() . '/write',
            body: $stream,
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }

    public function getWrites(): ?TupleKeysInterface
    {
        return $this->writes;
    }
}
