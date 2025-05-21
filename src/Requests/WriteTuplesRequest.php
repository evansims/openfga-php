<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class WriteTuplesRequest implements WriteTuplesRequestInterface
{
    /**
     * @param string                                     $store
     * @param string                                     $model
     * @param null|TupleKeysInterface<TupleKeyInterface> $writes
     * @param null|TupleKeysInterface<TupleKeyInterface> $deletes
     */
    public function __construct(
        private string $store,
        private string $model,
        private ?TupleKeysInterface $writes = null,
        private ?TupleKeysInterface $deletes = null,
    ) {
    }

    #[Override]
    public function getDeletes(): ?TupleKeysInterface
    {
        return $this->deletes;
    }

    #[Override]
    public function getModel(): string
    {
        return $this->model;
    }

    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = array_filter([
            'authorization_model_id' => $this->model,
            'writes' => $this->writes?->jsonSerialize(),
            'deletes' => $this->deletes?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/write',
            body: $stream,
        );
    }

    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }

    #[Override]
    public function getWrites(): ?TupleKeysInterface
    {
        return $this->writes;
    }
}
